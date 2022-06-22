<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Model\Catalog\Widget;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magenest\MobileApi\Model\Catalog\WidgetAbstract;
use Magento\Framework\EntityManager\Operation\Read\ReadExtensions;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory as BestSellersCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class Bestsellers
 * @package Magenest\MobileApi\Model\Catalog\Widget
 */
class Bestsellers extends WidgetAbstract
{
    /**
     * @var ProductSliderFactory
     */
    protected $_productSliderFactory;

    /**
     * @var BestSellersCollectionFactory
     */
    protected $_bestsellersCollectionFactory;

    /**
     * @var  CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ProductResource
     */
    protected $_resource;

    /**
     * Constructor.
     *
     * @param ReadExtensions $readExtensions
     * @param JoinProcessorInterface $joinProcessor
     * @param BestSellersCollectionFactory $bestsellersCollectionFactory
     * @param CollectionFactory $productCollectionFactory
     * @param ProductSliderFactory $productSliderFactory
     * @param StoreManagerInterface $storeManager
     * @param ProductResource $resource
     * @param array $data
     */
    function __construct(
        ReadExtensions $readExtensions,
        JoinProcessorInterface $joinProcessor,
        BestSellersCollectionFactory $bestsellersCollectionFactory,
        CollectionFactory $productCollectionFactory,
        ProductSliderFactory $productSliderFactory,
        StoreManagerInterface $storeManager,
        ProductResource $resource,
        array $data
    )
    {
        $this->_bestsellersCollectionFactory = $bestsellersCollectionFactory;
        $this->_productSliderFactory = $productSliderFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_resource = $resource;
        parent::__construct($readExtensions, $joinProcessor, $data);
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $data = $this->getData();
        $storeId = $this->_storeManager->getStore()->getId();
        $productIds = [];
        $bestsellers = $this->_bestsellersCollectionFactory->create()
            ->setPeriod('month')
            ->addStoreFilter($storeId);
        foreach ($bestsellers as $product) {
            $productIds[] = $product->getProductId();
        }
        if (!empty($productIds)) {
            $productIds = $this->_getAssociateProductIds($productIds);
            $collection = $this->_productCollectionFactory->create()->addIdFilter($productIds);
            $collection->addMinimalPrice()
                ->addAttributeToFilter('status', Status::STATUS_ENABLED)
                ->addFinalPrice()
                ->addTaxPercents()
                ->addAttributeToSelect('*')
                ->addStoreFilter($storeId);
            $this->processCollection($collection);
            return $this->_productSliderFactory->create()
                ->setTitle($data['title'])
                ->setItems(array_map(function ($id) use ($collection) {
                    return $collection->getItemById($id);
                }, $productIds))
                ->setTotalCount($collection->getSize())
                ->setType('product_slider');
        } else {
            return $this->_productSliderFactory->create()
                ->setTitle($data['title'])
                ->setItems([])
                ->setTotalCount(0)
                ->setType('product_slider');
        }
    }

    /**
     * Get associate product ids
     *
     * @param array $productIds
     * @return array
     */
    private function _getAssociateProductIds($productIds)
    {
        $tableName = 'bestseller_api_table_tmp';
        $connection = $this->_resource->getConnection();
        $table = $connection->newTable($tableName);
        $connection->dropTemporaryTable($tableName);

        $table->addColumn('left_id', Table::TYPE_INTEGER, null, ['nullable' => false, 'unsigned' => true], 'Entity ID');
        $connection->createTemporaryTable($table);
        $connection->insertArray($table->getName(), ['left_id'], $productIds);

        $query = $connection->select()
            ->from(['e' => $table->getName()])
            ->joinLeft(
                ['l' => $this->_resource->getTable('catalog_product_super_link')],
                'e.left_id = l.product_id',
                []
            )
            ->reset(\Magento\Framework\DB\Select::COLUMNS)
            ->columns(['IFNULL(l.parent_id, e.left_id) AS `Xxxid`']);

        $result = $connection->fetchCol($query);

        return $result;
    }
}
