<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\ProductLabel\Model\Indexer\Product;

use Magento\CatalogRule\Model\Indexer\AbstractIndexer;
use Magento\CatalogRule\Model\Indexer\IndexBuilder;
use Magenest\ProductLabel\Model\Indexer\IndexBuilder as Builder;

/**
 * Class ProductRuleIndexer
 * @package Magenest\ProductLabel\Model\Indexer\Product
 */
class ProductRuleIndexer extends AbstractIndexer
{

    /**
     * @var \Magenest\ProductLabel\Model\ResourceModel\LabelIndex
     */
    protected $_labelIndexResource;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var \Magenest\ProductLabel\Model\Indexer\LabelIndexer
     */
    protected $labelIndexer;

    /**
     * ProductRuleIndexer constructor.
     * @param IndexBuilder $indexBuilder
     * @param Builder $builder
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magenest\ProductLabel\Model\ResourceModel\LabelIndex $labelIndex
     * @param \Magenest\ProductLabel\Model\Indexer\LabelIndexer $labelIndexer
     */
    public function __construct(
        IndexBuilder $indexBuilder,
        Builder $builder,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magenest\ProductLabel\Model\ResourceModel\LabelIndex $labelIndex,
        \Magenest\ProductLabel\Model\Indexer\LabelIndexer $labelIndexer
    )
    {
        $this->_labelIndexResource = $labelIndex;
        $this->builder = $builder;
        $this->labelIndexer = $labelIndexer;
        parent::__construct($indexBuilder, $eventManager);
    }

    /**
     * {@inheritdoc}
     */
    public function doExecuteList($ids)
    {
        $idsOld = $this->_labelIndexResource->getAllProductIds();
        $this->builder->reindexFull($ids);
        $idsNew = $this->_labelIndexResource->getAllProductIds();
        $arrayProductIds = array_merge($idsOld, $idsNew);
        $this->labelIndexer->flushCacheByProductIds(array_unique($arrayProductIds));
    }

    protected function doExecuteRow($id)
    {
        // TODO: Implement doExecuteRow() method.
    }

    /**
     * @param $matchingProductIds
     * @param $discountRule
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function executeFullRule($matchingProductIds, $discountRule) {
        $this->builder->reindexFull($matchingProductIds, $discountRule);
    }
}
