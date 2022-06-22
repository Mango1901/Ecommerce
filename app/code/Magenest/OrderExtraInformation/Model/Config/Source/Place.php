<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderExtraInformation\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Place
 * @package Magenest\OrderExtraInformation\Model\Config\Source
 */
class Place implements ArrayInterface
{
    /** Const */
    const CART_PLACE = 'cart';
    const CART_ITEM_PLACE = 'cart_item';
    const CHECKOUT_PLACE = 'checkout';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::CART_PLACE, 'label' => __('Cart')],
            ['value' => self::CART_ITEM_PLACE, 'label' => __('Cart Item')],
            ['value' => self::CHECKOUT_PLACE, 'label' => __('Checkout')]
        ];
    }
}
