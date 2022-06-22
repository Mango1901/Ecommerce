<?php

namespace Magenest\NotificationBox\Ui\Component\Notification;

class OrderStatusReview implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('Complete'), 'value' => 'complete'],
            ['label' => __('Pending'), 'value' => 'pending'],
            ['label' => __('Processing'), 'value' => 'processing'],
        ];
    }
}
