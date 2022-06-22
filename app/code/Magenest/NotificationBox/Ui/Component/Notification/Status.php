<?php

namespace Magenest\NotificationBox\Ui\Component\Notification;

class Status implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('Active'), 'value' => 1],
            ['label' => __('Inactive'), 'value' => 0],
        ];
    }
}
