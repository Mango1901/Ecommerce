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

namespace Magenest\StoreCredit\Plugin\Block;

use Magenest\StoreCredit\Helper\Product;

/**
 * Class PdfItems
 * @package Magenest\StoreCredit\Plugin\Block
 */
class PdfItems
{
    /**
     * @var Product
     */
    protected $helper;

    /**
     * @param Product $helper
     */
    public function __construct(Product $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param \Magenest\PdfInvoice\Block\PdfItems $subject
     * @param array $result
     *
     * @return array
     */
    public function afterGetItemOptions(\Magenest\PdfInvoice\Block\PdfItems $subject, $result)
    {
        return $this->helper->getOptionList($subject->getItem(), $result);
    }
}
