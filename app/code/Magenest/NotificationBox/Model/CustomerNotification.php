<?php

namespace Magenest\NotificationBox\Model;

use Magento\Framework\Model\AbstractModel;

class CustomerNotification extends AbstractModel
{
    const STAR  = 1;
    const UNSTAR  = 0;
    const STATUS_READ = 1;
    const STATUS_UNREAD = 0;
    protected function _construct()
    {
        $this->_init('Magenest\NotificationBox\Model\ResourceModel\CustomerNotification');
    }
}
