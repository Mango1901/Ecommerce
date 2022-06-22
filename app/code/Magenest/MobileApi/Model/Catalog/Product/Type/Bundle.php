<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Model\Catalog\Product\Type;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class Virtual
 * @package Magenest\MobileApi\Model\Catalog\Product\Configurable
 */
class Bundle implements OptionsInterface
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * Price currency
     *
     * @param PriceCurrencyInterface $priceCurrency
     */
    function __construct(PriceCurrencyInterface $priceCurrency)
    {
        $this->_priceCurrency = $priceCurrency;
    }

    /**
     * @inheritdoc
     */
    public function process(ProductInterface $product)
    {
        $finalPrice = $product->getPriceInfo()->getPrice('final_price');
        $regularPrice = $product->getPriceInfo()->getPrice('regular_price');
        $extensionAttributes = $product->getExtensionAttributes();
        $pricing = $extensionAttributes->getPricing();
        $pricing->setFinalMinimalPrice($finalPrice->getMinimalPrice()->getValue())
            ->setFinalMaximalPrice($finalPrice->getMaximalPrice()->getValue())
            ->setRegularMinimalPrice($regularPrice->getMinimalPrice()->getValue())
            ->setRegularMaximalPrice($regularPrice->getMaximalPrice()->getValue());

        $extensionAttributes->setPricing($pricing);
        $product->setExtensionAttributes($extensionAttributes);
    }
}