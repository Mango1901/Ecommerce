<?php

namespace Magenest\RewardPoints\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class UpOrDown
 * @package Magenest\RewardPoints\Model\Source
 */
class UpOrDown implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'up',
                'label' => __('Round Up (Ceil)'),
            ],
            [
                'value' => 'down',
                'label' => __('Round Down (Floor)')
            ]
        ];
    }
}

