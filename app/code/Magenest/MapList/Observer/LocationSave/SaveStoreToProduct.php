<?php

namespace Magenest\MapList\Observer\LocationSave;

use Magento\Framework\Event\ObserverInterface;

class SaveStoreToProduct implements ObserverInterface
{
    protected $_storeProduct;

    protected $_storeProductCollection;

    protected $messageManager;

    protected $resource;

    protected $connection;

    public function __construct(
        \Magenest\MapList\Model\StoreProductFactory $storeProduct,
        \Magenest\MapList\Model\ResourceModel\StoreProduct\CollectionFactory $storeProductCollection,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_storeProduct = $storeProduct;
        $this->_storeProductCollection = $storeProductCollection;
        $this->connection = $resource->getConnection();
        $this->resource = $resource;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $idExist = array();

        $productListId = $observer->getData('product_list');
        $storeId =$observer->getData('id');

        $storeProductModelCollection = $this->_storeProductCollection->create()
            ->addFieldToFilter('location_id', $storeId)
            ->getData();

        $storeProductCollection = $this->_storeProductCollection->create();

        if (!empty($storeProductModelCollection)) {
            foreach ($storeProductModelCollection as $store) {
                //Find id not used and delete them
                if (!in_array($store['product_id'], $productListId)) {
                    foreach ($storeProductCollection as $productCollection) {
                        if ($productCollection->getData('store_product_id') == $store['store_product_id']) {
                            $productCollection->delete();
                        }
                    }
                } else {
                    //Find id already exists in the table
                    $idExist[] = $store['product_id'];
                }
            }

            //Comparison to find the id not in the table
            $idNew = array_diff($productListId, $idExist);
            $this->saveIdProduct($idNew, $storeId);
        } else {
            //if id doesn't exist in the table
            $this->saveIdProduct($productListId, $storeId);
        }
    }
    public function saveIdProduct($arrayId, $storeId)
    {
        $data=array();
        $tableName = $this->resource->getTableName('magenest_maplist_store_product');
        try {
            if (is_array($arrayId) && !empty($arrayId)) {
                foreach ($arrayId as $id) {
                    if ($id !=null) {
                        $data[] = array(
                            'location_id' => $storeId,
                            'product_id' => $id
                        );
                    }
                }

                $this->connection->insertMultiple($tableName, $data);
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
    }
}
