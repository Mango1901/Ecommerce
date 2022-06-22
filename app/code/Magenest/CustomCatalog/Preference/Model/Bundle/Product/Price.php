<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\CustomCatalog\Preference\Model\Bundle\Product;

use Magento\Catalog\Api\Data\ProductTierPriceExtensionFactory;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\CatalogRule\Model\ResourceModel\RuleFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Event\ManagerInterface;
use Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Helper\Data;
use Magento\Framework\Serialize\Serializer\Json;
use Magenest\CustomCatalog\Pricing\Currency;

/**
 * Price
 * @package Magenest\CustomCatalog\Preference\Model\Bundle\Product
 */
class Price extends \Magento\Bundle\Model\Product\Price
{
    /**
     * @var Currency
     */
    protected $_currency;

    /**
     * Constructor.
     *
     * @param RuleFactory $ruleFactory
     * @param StoreManagerInterface $storeManager
     * @param TimezoneInterface $localeDate
     * @param Session $customerSession
     * @param ManagerInterface $eventManager
     * @param PriceCurrencyInterface $priceCurrency
     * @param GroupManagementInterface $groupManagement
     * @param ProductTierPriceInterfaceFactory $tierPriceFactory
     * @param ScopeConfigInterface $config
     * @param Data $catalogData
     * @param Currency $currency
     * @param Json|null $serializer
     * @param ProductTierPriceExtensionFactory|null $tierPriceExtensionFactory
     */
    public function __construct(
        RuleFactory $ruleFactory,
        StoreManagerInterface $storeManager,
        TimezoneInterface $localeDate,
        Session $customerSession,
        ManagerInterface $eventManager,
        PriceCurrencyInterface $priceCurrency,
        GroupManagementInterface $groupManagement,
        ProductTierPriceInterfaceFactory $tierPriceFactory,
        ScopeConfigInterface $config,
        Data $catalogData,
        Currency $currency,
        Json $serializer = null,
        ProductTierPriceExtensionFactory $tierPriceExtensionFactory = null
    )
    {
        $this->_currency = $currency;
        parent::__construct(
            $ruleFactory,
            $storeManager,
            $localeDate,
            $customerSession,
            $eventManager,
            $priceCurrency,
            $groupManagement,
            $tierPriceFactory,
            $config,
            $catalogData,
            $serializer,
            $tierPriceExtensionFactory
        );
    }


    /**
     * {@inheritdoc}
     */
    protected function _applyTierPrice($product, $qty, $finalPrice)
    {
        $price = parent::_applyTierPrice($product, $qty, $finalPrice);
        $price = $this->_currency->roundPrice($price);

        return $price;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateSpecialPrice(
        $finalPrice,
        $specialPrice,
        $specialPriceFrom,
        $specialPriceTo,
        $store = null
    )
    {
        $price = parent::calculateSpecialPrice($finalPrice, $specialPrice, $specialPriceFrom, $specialPriceTo, $store);
        $price = $this->_currency->roundPrice($price);

        return $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getLowestPrice($bundleProduct, $price, $bundleQty = 1)
    {
        $price = parent::getLowestPrice($bundleProduct, $price, $bundleQty);
        $price = $this->_currency->roundPrice($price);

        return $price;
    }
}