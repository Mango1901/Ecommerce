<?php

namespace Magenest\SellOnInstagram\Ui\DataProvider;

use Magenest\SellOnInstagram\Model\Mapping;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magenest\SellOnInstagram\Model\ResourceModel\Mapping\CollectionFactory;

/**
 * Class MappingDataProvider
 * @package Magenest\SellOnInstagram\Ui\DataProvider
 */
class MappingDataProvider extends AbstractDataProvider
{
    /**
     * @var mixed
     */
    protected $collection;

    /**
     * TemplateDataProvider constructor.
     *
     * @param CollectionFactory $collection
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        CollectionFactory $collection,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $this->setCollection($collection);
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @param $collectionFactory
     *
     * @return void
     */
    private function setCollection($collectionFactory)
    {
        return $collectionFactory->create()->addFieldToFilter('type', Mapping::PRODUCT_TEMPLATE);
    }
}
