<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MobileApi\Model\Wishlist;

use Magenest\MobileApi\Api\Wishlist\WishlistManagementInterface;
use Magento\Wishlist\Controller\WishlistProvider;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory;
use Magento\Wishlist\Model\WishlistFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magenest\MobileApi\Api\Data\DataObjectInterfaceFactory;
use Magento\Wishlist\Model\ItemFactory;
use Symfony\Component\Config\Exception\LoaderLoadException;

/**
 * Class WishlistManagement
 * @package Magenest\MobileApi\Model\Wishlist
 */
class WishlistManagement implements WishlistManagementInterface
{
    /**
     * @var CollectionFactory
     */
    protected $_wishlistCollectionFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected $_productRepository;

    /**
     * @var WishlistFactory
     */
    protected $_wishlistFactory;

    /**
     * @var ItemFactory
     */
    protected $_itemFactory;

    /**
     * @var DataObjectInterfaceFactory
     */
    protected $_dataObjectFactory;

    /**
     * Constructor.
     *
     * @param CollectionFactory $wishlistCollectionFactory
     * @param WishlistFactory $wishlistFactory
     * @param ProductRepositoryInterface $productRepository
     * @param ItemFactory $itemFactory
     * @param DataObjectInterfaceFactory $dataObjectFactory
     */
    public function __construct(
        CollectionFactory $wishlistCollectionFactory,
        WishlistFactory $wishlistFactory,
        ProductRepositoryInterface $productRepository,
        ItemFactory $itemFactory,
        DataObjectInterfaceFactory $dataObjectFactory
    )
    {
        $this->_wishlistCollectionFactory = $wishlistCollectionFactory;
        $this->_productRepository = $productRepository;
        $this->_wishlistFactory = $wishlistFactory;
        $this->_itemFactory = $itemFactory;
        $this->_dataObjectFactory = $dataObjectFactory;
    }

    /**
     * @inheritdoc
     */
    public function getWishlistForCustomer($customerId)
    {
        $collection = $this->_wishlistCollectionFactory->create()->addCustomerIdFilter($customerId);
        $result = [];
        /** @var \Magento\Wishlist\Model\Item $item */
        foreach ($collection as $item) {
            $product = $item->getProduct();
            $result[] = [
                "wishlist_item_id" => $item->getWishlistItemId(),
                "wishlist_id" => $item->getWishlistId(),
                "product_id" => $item->getProductId(),
                "store_id" => $item->getStoreId(),
                "added_at" => $item->getAddedAt(),
                "description" => $item->getDescription(),
                "qty" => round($item->getQty()),
                'info_buyRequest' => \Zend_Json::decode($item->getOptionByCode('info_buyRequest')->getValue()),
                "product" => array_merge($product->toArray(), [
                    'final_price' => $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue(),
                    'regular_price' => $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue(),
                    'is_new' => (strtotime($product->getCreatedAt()) >= strtotime('-500 days'))
                ])
            ];
        }

        return $this->_dataObjectFactory->create()
            ->addData(['result' => $result])
            ->getData();
    }

    /**
     * @inheritdoc
     */
    public function addWishlistForCustomer($customerId, $productId, $buyRequest = null)
    {
        if ($productId == null) {
            throw new LocalizedException(__('Invalid product, Please select a valid product'));
        }

        $wishlist = $this->_wishlistFactory->create()->loadByCustomerId($customerId, true);
        $item = $wishlist->addNewItem($productId, $buyRequest);
        if(is_string($item)){
            throw new LoaderLoadException(__($item));
        }

        $wishlist->save();

        return $this->getWishlistForCustomer($customerId);
    }

    /**
     * @inheritdoc
     */
    public function deleteWishlistForCustomer($customerId, $wishlistItemId)
    {

        if ($wishlistItemId == null) {
            throw new LocalizedException(__('Invalid wishlist item, Please select a valid item'));
        }

        $item = $this->_itemFactory->create()->load($wishlistItemId);
        if (!$item->getId()) {
            throw new NoSuchEntityException(__('The requested Wish List Item doesn\'t exist.'));
        }

        $wishlistId = $item->getWishlistId();
        $wishlist = $this->_wishlistFactory->create();
        if ($wishlistId) {
            $wishlist->load($wishlistId);
        } elseif ($customerId) {
            $wishlist->loadByCustomerId($customerId, true);
        }

        if (!$wishlist->getId() || $wishlist->getCustomerId() != $customerId) {
            throw new NoSuchEntityException(__('The requested Wish List doesn\'t exist.'));
        }

        $item->delete();
        $wishlist->save();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getWishlistInfo($customerId)
    {
        if (empty($customerId) || !isset($customerId) || $customerId == "") {
            throw new InputException(__('Id required'));
        }

        $collection = $this->_wishlistCollectionFactory->create()->addCustomerIdFilter($customerId);
        $result[] = ["total_items" => count($collection)];

        return $this->_dataObjectFactory->create()
            ->addData(['result' => $result])
            ->getData();
    }
}