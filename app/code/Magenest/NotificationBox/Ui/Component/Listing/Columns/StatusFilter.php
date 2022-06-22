<?php

namespace Magenest\NotificationBox\Ui\Component\Listing\Columns;


class StatusFilter implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['label' => __('Enable'), 'value' => 1],
            ['label' => __('Disable'), 'value' => 0],
        ];

    }
}
