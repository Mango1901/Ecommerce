<?php

namespace Magenest\SellOnInstagram\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class History extends AbstractDb
{
    /**
     * Retrieve last inserted report id by user id
     *
     * @param string $userId
     *
     * @return int $lastId
     */
    public function getLastInsertedId($userId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from($this->getMainTable())->order($this->getIdFieldName() . ' DESC')->where('user_id = ?', $userId)->limit(1);

        return $connection->fetchOne($select);
    }

    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magenest_instagram_sync_history', 'history_id');
    }
}

