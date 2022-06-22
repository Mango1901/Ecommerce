<?php
namespace Magenest\RewardPoints\Model\Source;
use Magento\Framework\Option\ArrayInterface;
class CouponReferrer implements ArrayInterface{
    public function toOptionArray()
    {
        return [
            [
                'value' => 0,
                'label' => __('When the referred has signed-up '),
            ],
            [
                'value' => 1,
                'label' => __('When the referred has signed-up and made a purchase')
            ]
        ];
    }
}