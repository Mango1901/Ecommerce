<?php

namespace Magenest\RewardPoints\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Referral * @package Magenest\RewardPoints\Model\Source
 */
class Referral implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 0,
                'label' => __('The referrer'),
            ],
            [
                'value' => 1,
                'label' => __('The referred person')
            ],
            [
                'value' => 2,
                'label' => __('Both')
            ]
        ];
    }
}

