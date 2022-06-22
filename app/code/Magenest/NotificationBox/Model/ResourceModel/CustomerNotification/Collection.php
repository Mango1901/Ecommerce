<?php

namespace Magenest\NotificationBox\Model\ResourceModel\CustomerNotification;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Magenest\NotificationBox\Model\ResourceModel\Notification
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'entity_id';

    protected function _construct()
    {
        $this->_init('Magenest\NotificationBox\Model\CustomerNotification', 'Magenest\NotificationBox\Model\ResourceModel\CustomerNotification');
    }
}
