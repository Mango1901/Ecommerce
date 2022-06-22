<?php

namespace Magenest\SellOnInstagram\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Status implements ArrayInterface
{
    const SUCCESS = 1;
    const FAILED = 2;
    const PROCESSING = 3;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::SUCCESS, 'label' => __('Success')],
            ['value' => self::FAILED, 'label' => __('Failed')],
            ['value' => self::PROCESSING, 'label' => __('In Progress')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::SUCCESS => __('Success'),
            self::FAILED => __('Failed'),
            self::PROCESSING => __('In Progress')
        ];
    }
}
