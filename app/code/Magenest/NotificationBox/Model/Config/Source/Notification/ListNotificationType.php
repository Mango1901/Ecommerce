<?php

namespace Magenest\NotificationBox\Model\Config\Source\Notification;

use Magento\Framework\Option\ArrayInterface;
use Magento\Store\Model\StoreManagerInterface;

class ListNotificationType implements ArrayInterface
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
        $options = [];
        $listStore= $this->store->getGroups();
        foreach ($listStore as $store)
        {
            $options[] = ['value' => $store->getId(), 'label' => $store->getName()];
        }
        return $options;
    }
}
