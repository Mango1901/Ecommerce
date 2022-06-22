<?php

namespace Magenest\CustomCatalog\Model\Config\Source;

/**
 * @api
 * @since 100.0.2
 */
class YesNo implements \Magento\Framework\Data\OptionSourceInterface
{
    const VALUE_YES = 1;
    const VALUE_NO = 0;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Yes')],
            ['value' => 0, 'label' => __('No')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => __('No'), 1 => __('Yes')];
    }
}
