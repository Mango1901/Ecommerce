<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 29/10/2020 14:02
 */

namespace Magenest\RewardPoints\Model\ResourceModel;

use Magenest\RewardPoints\Api\Data\MembershipInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Membership extends AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(MembershipInterface::TABLE_NAME, MembershipInterface::ENTITY_ID);
    }
}