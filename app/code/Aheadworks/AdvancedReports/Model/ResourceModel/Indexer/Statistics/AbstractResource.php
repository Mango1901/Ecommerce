<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics;

use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Indexer\Table\StrategyInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Aheadworks\AdvancedReports\Model\Flag;
use Aheadworks\AdvancedReports\Model\FlagFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Eav\Api\AttributeRepositoryInterface;

/**
 * Class AbstractResource
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractResource extends \Magento\Indexer\Model\ResourceModel\AbstractResource
{
    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var AttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var Flag
     */
    private $reportsFlag;

    /**
     * @var string
     */
    private $catalogLinkField;

    /**
     * @var string
     */
    private $updatedAtFlag;

    /**
     * @var int
     */
    private $offset;

    protected $batchIds = [];

    /**
     * @param Context $context
     * @param TimezoneInterface $localeDate
     * @param StrategyInterface $tableStrategy
     * @param MetadataPool $metadataPool
     * @param AttributeRepositoryInterface $attributeRepository
     * @param FlagFactory $reportsFlagFactory
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        TimezoneInterface $localeDate,
        StrategyInterface $tableStrategy,
        MetadataPool $metadataPool,
        AttributeRepositoryInterface $attributeRepository,
        FlagFactory $reportsFlagFactory,
        $connectionName = null
    ) {
        parent::__construct($context, $tableStrategy, $connectionName);
        $this->localeDate = $localeDate;
        $this->metadataPool = $metadataPool;
        $this->attributeRepository = $attributeRepository;
        $this->reportsFlag = $reportsFlagFactory->create();
    }

    /**
     * Performs report
     *
     * @return null
     */
    abstract protected function process();

    /**
     * Reindex all
     *
     * @return $this
     */
    public function reindexAll()
    {
        $this->tableStrategy->setUseIdxTable(true);
        $this->clearTemporaryIndexTable();
        $this->beginTransaction();
        try {
            $this->process();
            $this->commit();
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
        $this->syncData();
        return $this;
    }

    public function reindexBatch()
    {
        $this->beginTransaction();
        try {
            $this->process();
            $this->commit();
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
        return $this;
    }

    public function getIdxTable($table = null)
    {
        if ($this->getBatchIds()) {
            return $this->getMainTable();
        }
        return parent::getIdxTable($table);
    }

    /**
     * {@inheritdoc}
     */
    public function clearTemporaryIndexTable()
    {
        $this->getConnection()->truncateTable($this->getIdxTable());
    }

    /**
     * Get period with Timezone offset from default config
     *
     * @param string $field
     * @param int|null $store
     * @return string
     */
    protected function getPeriod($field, $store = null)
    {
        return 'DATE(DATE_ADD(' . $field . ', INTERVAL ' . $this->getOffset($store) . ' SECOND))';
    }

    /**
     * Get catalog link field
     *
     * @return string
     * @throws \Exception
     */
    protected function getCatalogLinkField()
    {
        if (!$this->catalogLinkField) {
            $this->catalogLinkField = $this->metadataPool->getMetadata(CategoryInterface::class)->getLinkField();
        }
        return $this->catalogLinkField;
    }

    /**
     * Set disable_staging_preview part to select
     *
     * @return null
     */
    protected function disableStagingPreview()
    {
        if ($this->isPartExists('disable_staging_preview')) {
            $this->getConnection()->select()->setPart('disable_staging_preview', true);
        }
    }

    /**
     * Add filter by created date
     *
     * @param \Magento\Framework\DB\Select $select
     * @param string $tableAlias
     * @return null
     */
    protected function addFilterByCreatedAt($select, $tableAlias)
    {
        if ($this->getBatchIds()) {
            $select->where($tableAlias . '.entity_id IN (?)', $this->getBatchIds());
        } else {
            $select->where($tableAlias . '.created_at <= "' . $this->getUpdatedAtFlag() . '"');
        }
        return $select;
    }

    /**
     * Retrieve sql query to calculate the sum of bundle items
     *
     * @return \Zend_Db_Expr
     */
    protected function getBundleItemsPrice()
    {
        $select = $this->getConnection()->select()
            ->from(
                ['bundle_items' => $this->getTable('sales_order_item')],
                ['parent_item_id', 'order_id', new \Zend_Db_Expr('Sum(`bundle_items`.`price`) as price')]
            )->join(
                ['bundle' => $this->getTable('sales_order_item')],
                'bundle_items.parent_item_id = bundle.item_id AND bundle.product_type IN ("bundle")',
                []
            )->group(['bundle_items.parent_item_id', 'bundle_items.order_id']);

        return new \Zend_Db_Expr('(' . $select . ')') ;
    }

    /**
     * Is exists part key in select
     *
     * @param string $partKey
     * @return bool
     */
    private function isPartExists($partKey)
    {
        try {
            $this->getConnection()->select()->getPart($partKey);
        } catch (\Zend_Db_Select_Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Get updated date flag
     *
     * @param int|null $store
     * @return string
     */
    private function getUpdatedAtFlag()
    {
        if (!$this->updatedAtFlag) {
            $flag = $this->reportsFlag->setReportFlagCode(Flag::AW_AREP_STATISTICS_FLAG_CODE)->loadSelf();
            $this->updatedAtFlag = $flag->getLastUpdate();
        }
        return $this->updatedAtFlag;
    }

    /**
     * Get offset
     *
     * @param int|null $store
     * @return int
     */
    private function getOffset($store = null)
    {
        if (!$this->offset) {
            $timezone = $this->localeDate->scopeDate($store)->format('e');
            $this->offset = (new \DateTimeZone($timezone))->getOffset(new \DateTime());
        }
        return $this->offset;
    }

    /**
     * @return array
     */
    public function getBatchIds()
    {
        return $this->batchIds;
    }

    /**
     * @param array $batchIds
     * @return AbstractResource
     */
    public function setBatchIds(array $batchIds)
    {
        $this->batchIds = $batchIds;
        return $this;
    }
}
