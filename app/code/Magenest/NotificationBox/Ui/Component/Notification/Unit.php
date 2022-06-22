<?php

namespace Magenest\NotificationBox\Ui\Component\Notification;

class Unit implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('Minutes'), 'value' => 'minutes'],
            ['label' => __('Hours'), 'value' => 'hours'],
            ['label' => __('Days'), 'value' => 'days'],
            ['label' => __('Weeks'), 'value' => 'weeks']
        ];
    }
}
