<?php

namespace Magenest\NotificationBox\Model;

use Magento\Framework\Model\AbstractModel;

class NotificationQueue extends AbstractModel
{
    const IS_SENT = 1;
    const IS_NOT_SENT = 0;

    protected function _construct()
    {
        $this->_init('Magenest\NotificationBox\Model\ResourceModel\NotificationQueue');
    }
}
