<?php

namespace Magenest\SellOnInstagram\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Day implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Sunday'),
                'value' => '0',
            ],
            [
                'label' => __('Monday'),
                'value' => '1',
            ],
            [
                'label' => __('Tuesday'),
                'value' => '2',
            ],
            [
                'label' => __('Wednesday'),
                'value' => '3',
            ],
            [
                'label' => __('Thursday'),
                'value' => '4',
            ],
            [
                'label' => __('Friday'),
                'value' => '5',
            ],
            [
                'label' => __('Saturday'),
                'value' => '6',
            ],
        ];
    }
}
