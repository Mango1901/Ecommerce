<?php

namespace Magenest\SellOnInstagram\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class ActionType implements ArrayInterface
{
    const CREATE = 1;
    const DELETE = 2;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::CREATE, 'label' => __('Create & Update')],
            ['value' => self::DELETE, 'label' => __('Delete')],
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
            self::CREATE => __('Create & Update'),
            self::DELETE => __('Delete'),
        ];
    }
}
