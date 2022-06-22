<?php


namespace Magenest\Affiliate\Model\Campaign;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Discount
 * @package Magenest\Affiliate\Model\Campaign
 */
class Discount implements ArrayInterface
{
    const PERCENT = 'by_percent';
    const FIXED = 'by_fixed';
    const CART_FIXED = 'cart_fixed';
    const BUY_X_GET_Y = 'buy_x_get_y';

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::PERCENT,
                'label' => __('Percent of cart total')
            ],
            [
                'value' => self::CART_FIXED,
                'label' => __('Fixed amount discount for whole cart')
            ],
        ];

        return $options;
    }
}
