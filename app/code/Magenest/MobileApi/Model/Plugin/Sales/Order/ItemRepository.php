<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MobileApi\Model\Plugin\Sales\Order;

use Magento\Quote\Api\Data\CartItemExtensionInterfaceFactory;
use Magento\Sales\Api\Data\OrderItemExtensionInterfaceFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\Area;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Quote\Model\QuoteRepository;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magenest\MobileApi\Model\Helper;
use Magenest\MobileApi\Api\Data\Cart\ItemOptionsInterfaceFactory;

/**
 * Class ItemRepository
 * @package Magenest\MobileApi\Model\Plugin\Sales\Order
 */
class ItemRepository
{
    /**
     * @var CartItemExtensionInterfaceFactory
     */
    protected $_cartItemExtensionFactory;

    /**
     * @var  ProductRepository
     */
    protected $_productRepository;

    /**
     * @var OrderItemExtensionInterfaceFactory
     */
    protected $_orderItemExtensionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * @var ItemOptionsInterfaceFactory
     */
    protected $_itemOptionsFactory;

    /**
     * Constructor.
     *
     * @param ProductRepository $productRepository
     * @param StoreManagerInterface $storeManager
     * @param Helper $helper
     * @param OrderItemExtensionInterfaceFactory $orderItemExtensionFactory
     * @param ItemOptionsInterfaceFactory $itemOptionsFactory
     */
    function __construct(
        ProductRepository                  $productRepository,
        StoreManagerInterface              $storeManager,
        Helper                             $helper,
        OrderItemExtensionInterfaceFactory $orderItemExtensionFactory,
        ItemOptionsInterfaceFactory        $itemOptionsFactory
    ) {
        $this->_productRepository         = $productRepository;
        $this->_storeManager              = $storeManager;
        $this->_orderItemExtensionFactory = $orderItemExtensionFactory;
        $this->_helper                    = $helper;
        $this->_itemOptionsFactory        = $itemOptionsFactory;
    }

    /**
     * After Get list
     *
     * @param \Magento\Sales\Model\Order\ItemRepository $subject
     * @param OrderItemInterface[] $result
     *
     * @return OrderItemInterface[]
     */
    public function afterGetList(\Magento\Sales\Model\Order\ItemRepository $subject, $result)
    {
        foreach ($result as $item) {
            $product    = $this->_productRepository->getById($item->getProductId(), false, $item->getStoreId());
            $attributes = $item->getExtensionAttributes();
            if ($attributes === null) {
                $attributes = $this->_orderItemExtensionFactory->create();
            }

            if ($item->getProductType() == 'configurable') {
                $options = array_map(function ($option) {
                    return $this->_itemOptionsFactory->create()->setLabel($option['label'])->setValue($option['value']);
                }, $item->getProductOptions()['attributes_info']);

                $attributes->setOptions($options);
            }

            $attributes->setThumbnail($this->_helper->getImageUrl($product, 'product_base_image'));
        }

        return $result;
    }
}
