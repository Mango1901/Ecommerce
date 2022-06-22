<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 04/11/2021
 * Time: 13:40
 */

namespace Magenest\AffiliateCatalogRule\Pricing\Price;

use Magenest\AffiliateCatalogRule\Model\ResourceModel\AffiliateRule;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Pricing\Price\LowestPriceOptionsProviderInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Pricing\Adjustment\Calculator;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;

class ConfigurableOriginRulePrice extends OriginRulePrice
{
    /**
     * @var LowestPriceOptionsProviderInterface
     */
    protected $lowestPriceOptionsProvider;

    public function __construct(
        Product $saleableItem,
        $quantity,
        Calculator $calculator,
        PriceCurrencyInterface $priceCurrency,
        TimezoneInterface $dateTime,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        AffiliateRule $ruleResource,
        LowestPriceOptionsProviderInterface $lowestPriceOptionsProvider
    ) {
        $this->lowestPriceOptionsProvider = $lowestPriceOptionsProvider;
        parent::__construct($saleableItem, $quantity, $calculator, $priceCurrency, $dateTime, $storeManager, $customerSession, $ruleResource);
    }

    public function getValue()
    {
        $price = null;

        foreach ($this->lowestPriceOptionsProvider->getProducts($this->product) as $subProduct) {
            $productPrice = $subProduct->getPriceInfo()->getPrice('origin_rule_price')->getValue();
            $price = isset($price) ? min($price, $productPrice) : $productPrice;
        }

        return (float)$price;
    }
}
