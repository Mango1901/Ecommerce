<?php

namespace Magenest\GiaoHangTietKiem\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class PriceType  implements ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Fixed')],
            ['value' => 0, 'label' => __('Dynamic')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => __('Dynamic'), 1 => __('Fixed')];
    }
}
