<?php


namespace Magenest\API247\Model\Config\Source;


use Magento\Framework\Option\ArrayInterface;

class TestMode implements ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Production')],
            ['value' => 1, 'label' => __('Sandbox Staging')],

        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => __('Production'), 1 => __('Sandbox Staging')];
    }
}
