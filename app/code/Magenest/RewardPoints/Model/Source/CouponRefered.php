<?php
namespace Magenest\RewardPoints\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class CouponRefered implements ArrayInterface {
    public function toOptionArray()
    {
        return [
            [
                'value' => 0,
                'label' => __('Sign up a new account'),
            ],
            [
                'value' => 1,
                'label' => __('After Sign up and make a purchase')
            ]
        ];
    }
}