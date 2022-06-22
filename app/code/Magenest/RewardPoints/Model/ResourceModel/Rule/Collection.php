<?php

namespace Magenest\RewardPoints\Model\ResourceModel\Rule;

use Magenest\RewardPoints\Api\Data\NotificationInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Magenest\RewardPoints\Model\ResourceModel\Rule
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('Magenest\RewardPoints\Model\Rule', 'Magenest\RewardPoints\Model\ResourceModel\Rule');
    }

    protected function _initSelect()
    {
        $this->getSelect()->from(['main_table' => $this->getMainTable()])
            ->joinLeft(['notification' => $this->getTable(NotificationInterface::TABLE_NAME)],
                'main_table.id' . ' = notification.' . NotificationInterface::RULE_ID,
                ['notification_id' => NotificationInterface::ENTITY_ID, NotificationInterface::NOTIFICATION_STATUS, NotificationInterface::NOTIFICATION_DISPLAY_POSITION, NotificationInterface::NOTIFICATION_CONTENT]
            );

        return $this;
    }
}
