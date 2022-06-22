<?php

namespace Magenest\RewardPoints\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class UpOrDown
 * @package Magenest\RewardPoints\Model\Source
 */
class ConsumerSpending implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => null,
                'label' => __('-- Please Select --'),
            ],
            [
                'value' => '1',
                'label' => __('Fixed number'),
            ],
            [
                'value' => '2',
                'label' => __('Percent of total order value')
            ]
        ];
    }
}

