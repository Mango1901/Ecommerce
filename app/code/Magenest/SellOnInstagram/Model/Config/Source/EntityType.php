<?php

namespace Magenest\SellOnInstagram\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class EntityType implements ArrayInterface
{
    const PRODUCT = 1;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::PRODUCT, 'label' => __('Product')]
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
            self::PRODUCT => __('Product')
        ];
    }
}
