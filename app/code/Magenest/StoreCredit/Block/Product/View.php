<?php
/**
 * Magenest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magenest.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magenest.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magenest
 * @package     Magenest_StoreCredit
 * @copyright   Copyright (c) Magenest (https://www.magenest.com/)
 * @license     https://www.magenest.com/LICENSE.txt
 */

namespace Magenest\StoreCredit\Block\Product;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\View\AbstractView;
use Magento\Framework\Locale\FormatInterface as LocaleFormat;
use Magento\Framework\Stdlib\ArrayUtils;
use Magenest\StoreCredit\Helper\Data;

/**
 * Class View
 * @package Magenest\StoreCredit\Block\Product
 */
class View extends AbstractView
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var LocaleFormat
     */
    protected $localeFormat;

    /**
     * View constructor.
     *
     * @param Context $context
     * @param ArrayUtils $arrayUtils
     * @param Data $helper
     * @param LocaleFormat $localeFormat
     * @param array $data
     */
    public function __construct(
        Context $context,
        ArrayUtils $arrayUtils,
        Data $helper,
        LocaleFormat $localeFormat,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->localeFormat = $localeFormat;

        parent::__construct($context, $arrayUtils, $data);
    }

    /**
     * @return array
     */
    public function getInformation()
    {
        $product = $this->getProduct();

        $information = [
            'productId' => $product->getId(),
            'creditAmount' => $this->convertPrice($product->getCreditAmount()),
            'creditRate' => $product->getCreditRate() ? $product->getCreditRate() / 100 : 1,
            'creditRange' => !!$product->getAllowCreditRange(),
            'minCredit' => $this->convertPrice($product->getMinCredit()),
            'maxCredit' => $this->convertPrice($product->getMaxCredit()),
            'currencyRate' => $this->convertPrice(1),
            'priceFormat' => $this->localeFormat->getPriceFormat(),
        ];

        return $information;
    }

    /**
     * @param $amount
     *
     * @return string
     */
    public function formatPrice($amount)
    {
        return $this->helper->formatPrice($amount, false);
    }

    /**
     * @param $amount
     *
     * @return string
     */
    public function convertPrice($amount)
    {
        return $this->helper->convertPrice($amount, false);
    }
}
