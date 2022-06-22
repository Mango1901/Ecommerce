<?php

namespace Magenest\NotificationBox\Model\ResourceModel\Notification;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Magenest\NotificationBox\Model\ResourceModel\Notification
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init('Magenest\NotificationBox\Model\Notification', 'Magenest\NotificationBox\Model\ResourceModel\Notification');
    }
}
