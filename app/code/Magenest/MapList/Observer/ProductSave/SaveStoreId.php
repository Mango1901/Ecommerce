<?php

namespace Magenest\MapList\Observer\ProductSave;

use Magento\Framework\Event\ObserverInterface;
use Magento\Backend\App\Action;

class SaveStoreId implements ObserverInterface
{
    protected $_storeProduct;

    protected $_storeProductCollection;

    protected $messageManager;

    protected $metadataPool;

    protected $resource;

    protected $connection;

    public function __construct(
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magenest\MapList\Model\StoreProductFactory $storeProductFactory,
        \Magenest\MapList\Model\ResourceModel\StoreProduct\CollectionFactory $storeProductCollection,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->_storeProduct = $storeProductFactory;
        $this->_storeProductCollection = $storeProductCollection;
        $this->metadataPool = $metadataPool;
        $this->connection = $resource->getConnection();
        $this->resource = $resource;
        $this->messageManager = $messageManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $idExist = array();

        $entityMetadata = $this->metadataPool->getMetadata(\Magento\Catalog\Api\Data\ProductInterface::class);
        $entityId = $entityMetadata->getLinkField();

        $product = $observer->getData('product');
        $productId = $product->getData($entityId);

        $productModelCollection = $this->_storeProductCollection->create()
            ->addFieldToFilter('product_id', $productId)
            ->getData();

        $storeProductCollection = $this->_storeProductCollection->create();

        $storesIdString = $product->getStores();
        if ($storesIdString) {
            $storesId = explode(',', $storesIdString);

            if (!empty($productModelCollection)) {
                foreach ($productModelCollection as $product) {
                    //Find id not used and delete them
                    if (!in_array($product['location_id'], $storesId)) {
                        foreach ($storeProductCollection as $productCollection) {
                            if ($productCollection->getData('store_product_id') == $product['store_product_id']) {
                                $productCollection->delete();
                            }
                        }
                    } else {
                        //Find id already exists in the table
                        $idExist[] = $product['location_id'];
                    }
                }

                //Comparison to find the id not in the table
                $idNew = array_diff($storesId, $idExist);
                $this->saveIdStore($idNew, $productId);
            } else {
                //if id doesn't exist in the table
                $this->saveIdStore($storesId, $productId);
            }
        } else {
            $this->saveIdStore(null, $productId);
        }
    }

    public function saveIdStore($arrayId, $productId)
    {
        try {
            $data=array();
            $tableName = $this->resource->getTableName('magenest_maplist_store_product');
            if (is_array($arrayId) && !empty($arrayId) && $arrayId != null) {
                foreach ($arrayId as $id) {
                    $data[] = array(
                        'location_id' => $id,
                        'product_id' => $productId
                    );
                }

                $this->connection->insertMultiple($tableName, $data);
            } else {
                $storeProductCollection = $this->_storeProductCollection->create();
                foreach ($storeProductCollection as $productCollection) {
                    if ($productCollection->getData('product_id') == $productId) {
                        $productCollection->delete();
                    }
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
    }
}
