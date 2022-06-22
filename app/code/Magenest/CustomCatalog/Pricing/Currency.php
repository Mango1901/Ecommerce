<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\CustomCatalog\Pricing;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Form\Field;
use Magento\Framework\Locale\ResolverInterface;
use Zend\I18n\Filter\NumberParse;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class Currency
 * @package Magenest\CustomCatalog\Pricing
 */
class Currency
{
    /** @const - Default precision */
    const DEFAULT_PRECISION = -2;

    /**
     * @var  NumberParse
     */
    protected $_filter;

    /**
     * @var PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * Constructor.
     *
     * @param ResolverInterface $localeResolver
     * @param PriceCurrencyInterface $priceCurrency
     */
    function __construct(
        ResolverInterface $localeResolver,
        PriceCurrencyInterface $priceCurrency
    )
    {
        $this->_priceCurrency = $priceCurrency;
        $this->_filter = new NumberParse($localeResolver->getLocale());
    }

    /**
     * Round price
     *
     * @param float $price
     * @return int
     */
    public function roundPrice($price)
    {
        $price = $this->_filter->filter($price);
        $price = $this->_priceCurrency->roundPrice($price, self::DEFAULT_PRECISION);

        return $price;
    }
}