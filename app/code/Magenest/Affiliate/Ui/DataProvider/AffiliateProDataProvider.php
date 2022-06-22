<?php


namespace Magenest\Affiliate\Ui\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Magenest\Affiliate\Model\ResourceModel\Banner\CollectionFactory;

/**
 * Class AffiliateDataProvider
 * @package Magenest\Affiliate\Ui\DataProvider
 */
class AffiliateDataProvider extends AbstractDataProvider
{
    /**
     * AffiliateDataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->collection = $collectionFactory->create();
    }

    /**
     * @return array
     */
    public function getData()
    {
        return parent::getData();
    }
}
