<?php
/**
 * Copyright (c) Magenest, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\FacebookSupportLive\Model\Config\Source;

class Type implements \Magento\Framework\Option\ArrayInterface
{
    const USE_SETTINGS = 1;
    const USE_CODE     = 2;

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 1,
                'label' => __('Use Settings')
            ],
            [
                'value' => 2,
                'label' => __('Use Code')
            ]
        ];
    }
}