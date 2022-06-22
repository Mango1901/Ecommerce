<?php

namespace Magenest\NotificationBox\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class NotificationQueue
 * @package Magenest\NotificationBox\Model\ResourceModel
 */
class NotificationQueue extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('magenest_notification_queue', 'entity_id');
    }
}
