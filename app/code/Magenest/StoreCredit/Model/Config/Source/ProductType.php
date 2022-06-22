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

namespace Magenest\StoreCredit\Model\Config\Source;

use Magento\Catalog\Model\Product\Type;
use Magenest\StoreCredit\Model\Product\Type\StoreCredit;

/**
 * Class ProductType
 *
 * @package Magenest\StoreCredit\Model\Config\Source
 */
class ProductType extends Type
{
    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public function getOptionArray()
    {
        $options = parent::getOptionArray();

        unset($options[StoreCredit::TYPE_STORE_CREDIT]);

        return $options;
    }
}
