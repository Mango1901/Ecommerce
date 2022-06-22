<?php

namespace Magenest\NotificationBox\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Notification
 * @package Magenest\NotificationBox\Model\ResourceModel
 */
class Notification extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('magenest_notification', 'id');
    }
}
