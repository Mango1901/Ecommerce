<?php

/**
 * Used in creating options for Yes|No config value selection
 *
 */
namespace Magenest\Promobar\Model\Config\Source;

class BorderStyle
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            '' => __('--Select--'),
            'none' => __('None'),
            'solid' => __('Solid'),
            'dotted' => __('Dotted'),
            'dashed' => __('Dashed'),
            'double' => __('Double'),
        ];
    }
}
