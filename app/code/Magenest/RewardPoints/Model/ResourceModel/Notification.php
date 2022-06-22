<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 06/11/2020 15:09
 */

namespace Magenest\RewardPoints\Model\ResourceModel;

use Magenest\RewardPoints\Api\Data\NotificationInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Notification extends AbstractDb
{
    protected $_idFieldName = NotificationInterface::ENTITY_ID;

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(NotificationInterface::TABLE_NAME, NotificationInterface::ENTITY_ID);
    }
}