<?php
namespace Magenest\RewardPoints\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class ReferralCoupon implements ArrayInterface {
    public function toOptionArray()
    {
        return [
            [
                'value' => 0,
                'label' => __('Both'),
            ],
            [
                'value' => 1,
                'label' => __('The Referrer')
            ],
            [
                'value' => 2,
                'label' => __('The Referee')
            ]
        ];
    }
}
