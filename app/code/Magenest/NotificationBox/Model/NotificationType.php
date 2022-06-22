<?php

namespace Magenest\NotificationBox\Model;

use Magento\Framework\Model\AbstractModel;

class NotificationType extends AbstractModel
{

    const IS_CATEGORY = 1;
    const IS_NOT_CATEGORY = 0;
    protected function _construct()
    {
        $this->_init('Magenest\NotificationBox\Model\ResourceModel\NotificationType');
    }
}
