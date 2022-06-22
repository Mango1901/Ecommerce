<?php

namespace Magenest\NotificationBox\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class NotificationType
 * @package Magenest\NotificationBox\Model\ResourceModel;
 */
class NotificationType extends AbstractDb
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magenest_notification_type', 'entity_id');
    }
}
