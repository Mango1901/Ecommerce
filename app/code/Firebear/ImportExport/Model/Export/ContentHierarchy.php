<?php
/**
 * @copyright: Copyright © 2019 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model\Export;

use Firebear\ImportExport\Model\Export\ContentHierarchy\AttributeCollection;
use Firebear\ImportExport\Model\Export\FilterProcessor\FilterProcessorAggregator;
use Firebear\ImportExport\Model\ExportJob\Processor;
use Firebear\ImportExport\Traits\Export\Entity as ExportTrait;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Collection\AbstractCollection as EavAbstractCollection;
use Magento\ImportExport\Model\Export;
use Magento\ImportExport\Model\Export\AbstractEntity;
use Magento\ImportExport\Model\Export\Factory as ExportFactory;
use Magento\ImportExport\Model\ResourceModel\CollectionByPagesIteratorFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\VersionsCms\Api\Data\HierarchyNodeInterface as NodeInterface;
use Magento\VersionsCms\Model\ResourceModel\Hierarchy\Node\Collection as NodeCollection;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class ContentHierarchy
 *
 * @package Firebear\ImportExport\Model\Export
 */
class ContentHierarchy extends AbstractEntity implements EntityInterface
{
    use ExportTrait;

    /**
     * Entity type code
     */
    const ENTITY_TYPE_CODE = 'content_hierarchy';

    /**
     * Attribute collection name
     */
    const ATTRIBUTE_COLLECTION_NAME = AttributeCollection::class;

    /**
     * @var ExportFactory
     */
    private $exportFactory;

    /**
     * @var NodeCollection
     */
    private $entityCollection;

    /**
     * @var FilterProcessorAggregator
     */
    private $filterProcessor;

    /**
     * ContentHierarchy constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param ExportFactory $exportFactory
     * @param CollectionByPagesIteratorFactory $resourceColFactory
     * @param FilterProcessorAggregator $filterProcessor
     * @param LoggerInterface $logger
     * @param ConsoleOutput $output
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        ExportFactory $exportFactory,
        CollectionByPagesIteratorFactory $resourceColFactory,
        FilterProcessorAggregator $filterProcessor,
        LoggerInterface $logger,
        ConsoleOutput $output,
        array $data = []
    ) {
        $this->exportFactory = $exportFactory;
        $this->filterProcessor = $filterProcessor;
        $this->_logger = $logger;
        $this->output = $output;
        parent::__construct($scopeConfig, $storeManager, $exportFactory, $resourceColFactory, $data);
    }

    /**
     * Entity type code getter
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return self::ENTITY_TYPE_CODE;
    }

    /**
     * Export process
     *
     * @return array
     * @throws LocalizedException
     */
    public function export()
    {
        //Execution time may be very long
        set_time_limit(0);

        $writer = $this->getWriter();
        $writer->setHeaderCols($this->_getHeaderColumns());

        $collection = $this->_getEntityCollection(true);
        $this->_prepareEntityCollection($collection);
        $this->_exportCollectionByPages($collection);

        return [
            $writer->getContents(),
            $this->_processedEntitiesCount,
            $this->lastEntityId
        ];
    }

    /**
     * Export one item
     *
     * @param AbstractModel $item
     * @return void
     * @throws LocalizedException
     */
    public function exportItem($item)
    {
        $exportData = $this->changeRow($item->getData());
        $this->getWriter()->writeRow($exportData);
        $this->lastEntityId = $item->getId();
        $this->_processedEntitiesCount++;
    }

    /**
     * Retrieve header columns
     *
     * @return array
     */
    protected function _getHeaderColumns()
    {
        $columns = [];
        $attributeCollection = $this->getAttributeCollection();
        foreach ($attributeCollection as $attribute) {
            $columns[] = $attribute->getAttributeCode();
        }

        return $this->changeHeaders($columns);
    }

    /**
     * Retrieve entity collection
     *
     * @param bool $resetCollection
     * @return AbstractCollection|EavAbstractCollection|NodeCollection
     */
    protected function _getEntityCollection($resetCollection = false)
    {
        if ($resetCollection || empty($this->entityCollection)) {
            $this->entityCollection = $this->exportFactory->create(NodeCollection::class);
            $this->entityCollection->joinMetaData();
        }

        return $this->entityCollection;
    }

    /**
     * Apply filter to collection
     *
     * @param AbstractCollection|EavAbstractCollection $collection
     * @return EavAbstractCollection|EavAbstractCollection
     * @throws LocalizedException
     */
    protected function _prepareEntityCollection($collection)
    {
        $idField = 'main_table.' . NodeInterface::NODE_ID;
        $collection->setOrder($idField, Collection::SORT_ORDER_ASC);

        if ($this->_parameters[Processor::LAST_ENTITY_SWITCH] > 0) {
            $lastEntityId = $this->_parameters[Processor::LAST_ENTITY_ID];
            $this->filterProcessor->process('lastentity', $collection, $idField, $lastEntityId);
        }

        $exportFilter = $this->retrieveFilterData($this->_parameters);
        if (!empty($exportFilter)) {
            $attributeCollection = $this->getAttributeCollection();
            $attributes = $attributeCollection->getItems();
            foreach ($exportFilter as $code => $value) {
                if (isset($attributes[$code])) {
                    /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
                    $attribute = $attributes[$code];
                    $attributeFilterType = Export::getAttributeFilterType($attribute);
                    $columnName = $attribute->getData('backend_table_name') . '.' . $attribute->getId();
                    $this->filterProcessor->process($attributeFilterType, $collection, $columnName, $value);
                }
            }
        }

        return $collection;
    }

    /**
     * Retrieve filters data
     *
     * @param array $filters
     * @return array
     */
    private function retrieveFilterData(array $filters)
    {
        $filterData = array_filter(
            $filters[Processor::EXPORT_FILTER] ?? [],
            function ($value) {
                return $value !== '';
            }
        );

        return $filterData;
    }

    /**
     * Retrieve entity field for filter
     *
     * @return array
     */
    public function getFieldsForFilter()
    {
        $fields = [];
        $attributeCollection = $this->getAttributeCollection();
        foreach ($attributeCollection as $attribute) {
            $fields[] = [
                'label' => $attribute->getDefaultFrontendLabel(),
                'value' => $attribute->getAttributeCode()
            ];
        }

        return [$this->getEntityTypeCode() => $fields];
    }

    /**
     * Retrieve entity field for export
     *
     * @return array
     */
    public function getFieldsForExport()
    {
        $fields = [];
        $attributeCollection = $this->getAttributeCollection();
        foreach ($attributeCollection as $attribute) {
            $fields[] = $attribute->getAttributeCode();
        }

        return $fields;
    }

    /**
     * Retrieve entity field columns
     *
     * @return array
     */
    public function getFieldColumns()
    {
        $fields = [];
        $attributeCollection = $this->getAttributeCollection();
        foreach ($attributeCollection as $attribute) {
            /** @var AbstractSource $source */
            $fields[] = [
                'field' => $attribute->getAttributeCode(),
                'type' => $attribute->getFrontendInput(),
                'select' => ($attribute->getSourceModel() && $source = $attribute->getSource())
                    ? $source->toOptionArray()
                    : []
            ];
        }

        return [$this->getEntityTypeCode() => $fields];
    }

    /**
     * Retrieve attributes codes which are appropriate for export
     *
     * @return array
     */
    protected function _getExportAttrCodes()
    {
        return [];
    }
}
