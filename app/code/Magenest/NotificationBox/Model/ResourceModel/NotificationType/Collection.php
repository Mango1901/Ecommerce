<?php

namespace Magenest\NotificationBox\Model\ResourceModel\NotificationType;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Magenest\NotificationBox\Model\ResourceModel\NotificationType;
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'entity_id';

    protected function _construct()
    {
        $this->_init('Magenest\NotificationBox\Model\NotificationType', 'Magenest\NotificationBox\Model\ResourceModel\NotificationType');
    }
}
