<?php

namespace Magenest\CustomCatalog\Model\Config\Source;

/**
 * @api
 * @since 100.0.2
 */
class TypeSaleLabel implements \Magento\Framework\Data\OptionSourceInterface
{
    const VALUE_LABEL = 1;
    const VALUE_PERCENT = 0;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Label')],
            ['value' => 0, 'label' => __('Percent(%)')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => __('Percent(%)'), 1 => __('Label')];
    }
}
