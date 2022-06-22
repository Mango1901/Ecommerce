<?php

namespace Magenest\NotificationBox\Ui\Component\Notification;

class SendTimeOptions implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('Send immediately'), 'value' => 'send_immediately'],
            ['label' => __('Schedule time'), 'value' => 'schedule_time'],
            ['label' => __('Send after the trigger condition'), 'value' => 'send_after_the_trigger_condition']
        ];
    }
}
