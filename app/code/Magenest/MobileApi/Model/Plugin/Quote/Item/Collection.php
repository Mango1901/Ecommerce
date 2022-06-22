<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MobileApi\Model\Plugin\Quote\Item;

use Magento\Quote\Api\Data\CartItemExtensionInterfaceFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\Area;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Quote\Model\QuoteRepository;
use Magento\Bundle\Helper\Catalog\Product\Configuration as BundleConfig;
use Magento\Catalog\Helper\Product\ConfigurationPool;
use Magenest\MobileApi\Model\Cart\ItemOptionsFactory;
use Magenest\MobileApi\Model\Helper;

/**
 * Class Item
 * @package Magenest\MobileApi\Model\Plugin\Quote\Item
 */
class Collection
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
     * @var BundleConfig
     */
    protected $_bundleConfig;

    /**
     * @var QuoteRepository
     */
    protected $_quoteRepository;

    /**
     * @var ConfigurationPool
     */
    protected $_configurationPool;

    /**
     * @var ItemOptionsFactory
     */
    protected $_itemOptionsFactory;

    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * Constructor.
     *
     * @param CartItemExtensionInterfaceFactory $cartItemExtensionInterfaceFactory
     * @param ProductRepository $productRepository
     * @param StoreManagerInterface $storeManager
     * @param BundleConfig $bundleConfig
     * @param QuoteRepository $quoteRepository
     * @param ItemOptionsFactory $itemOptionsFactory
     * @param Helper $helper
     * @param ConfigurationPool $configurationPool
     */
    function __construct(
        CartItemExtensionInterfaceFactory $cartItemExtensionInterfaceFactory,
        ProductRepository $productRepository,
        StoreManagerInterface $storeManager,
        BundleConfig $bundleConfig,
        QuoteRepository $quoteRepository,
        ItemOptionsFactory $itemOptionsFactory,
        Helper $helper,
        ConfigurationPool $configurationPool
    )
    {
        $this->_cartItemExtensionFactory = $cartItemExtensionInterfaceFactory;
        $this->_productRepository = $productRepository;
        $this->_storeManager = $storeManager;
        $this->_bundleConfig = $bundleConfig;
        $this->_quoteRepository = $quoteRepository;
        $this->_configurationPool = $configurationPool;
        $this->_itemOptionsFactory = $itemOptionsFactory;
        $this->_helper = $helper;
    }

    /**
     * After Load With Filter
     *
     * @param \Magento\Quote\Model\ResourceModel\Quote\Item\Collection $subject
     * @param \Magento\Quote\Model\ResourceModel\Quote\Item\Collection $result
     * @return \Magento\Quote\Model\Quote\Item
     */
    public function afterLoadWithFilter(\Magento\Quote\Model\ResourceModel\Quote\Item\Collection $subject, $result)
    {
        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($result->getItems() as $item) {
            try {
                $attributes = $item->getExtensionAttributes();
                if ($attributes === null) {
                    $attributes = $this->_cartItemExtensionFactory->create();
                }

                $product = $this->_productRepository->getById($item->getProductId(), false, $item->getStoreId());
                $attributes->setThumbnail($this->_helper->getImageUrl($product, 'product_base_image'));
                $attributes->setMagenestProductId($item->getProductId());
                $attributes->setProductOptions($this->_getFormattedOptionValue($item));
                $attributes->setMessages(array_map(function ($message) {
                    return __($message);
                }, $item->getMessage(false)));

                $item->setExtensionAttributes($attributes);
            } catch (\Exception $e) {
            }
        }

        return $result;
    }

    /**
     * Retrieve formatted item options view
     *
     * @param \Magento\Quote\Api\Data\CartItemInterface $item
     * @return array
     */
    protected function _getFormattedOptionValue($item)
    {
        $optionsData = [];
        $options = $this->_configurationPool->getByProductType($item->getProductType())->getOptions($item);
        foreach ($options as $index => $optionValue) {
            /* @var $helper \Magento\Catalog\Helper\Product\Configuration */
            $helper = $this->_configurationPool->getByProductType('default');
            $params = [
                'max_length' => 55,
                'cut_replacer' => ' <a href="#" class="dots tooltip toggle" onclick="return false">...</a>'
            ];
            $option = $helper->getFormattedOptionValue($optionValue, $params);
            $optionsData[$index] = $this->_itemOptionsFactory->create()
                ->setLabel($optionValue['label'])
                ->setValue($option['value']);
        }

        return $optionsData;
    }
}
