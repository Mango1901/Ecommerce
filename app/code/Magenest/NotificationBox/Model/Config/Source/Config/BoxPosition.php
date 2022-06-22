<?php

namespace Magenest\NotificationBox\Model\Config\Source\Config;

use Magento\Framework\Option\ArrayInterface;
use Magento\Store\Model\StoreManagerInterface;

class BoxPosition implements ArrayInterface
{
    /** @var StoreManagerInterface  */
    protected $store;

    /**
     * @param StoreManagerInterface $storeManage
     */
    public function __construct(StoreManagerInterface $storeManage)
    {
        $this->store = $storeManage;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => "magenest-notification-box--center", 'label' => __("Center"),],
            ['value' => "magenest-notification-box--left", 'label' => __("Left"),],
            ['value' => "magenest-notification-box--right", 'label' => __("Right"),],
        ];
    }
}
