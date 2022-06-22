<?php

namespace Magenest\NotificationBox\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Customer Token
 * @package Magenest\NotificationBox\Model\ResourceModel
 */
class CustomerToken extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('magenest_customer_token', 'entity_id');
    }
}
