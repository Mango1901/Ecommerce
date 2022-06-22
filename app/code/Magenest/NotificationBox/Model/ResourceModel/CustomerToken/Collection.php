<?php

namespace Magenest\NotificationBox\Model\ResourceModel\CustomerToken;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Magenest\NotificationBox\Model\ResourceModel\CustomerToken;
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'entity_id';

    protected function _construct()
    {
        $this->_init('Magenest\NotificationBox\Model\CustomerToken', 'Magenest\NotificationBox\Model\ResourceModel\CustomerToken');
    }
}
