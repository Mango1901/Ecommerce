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

namespace Magenest\RewardPoints\Model\ResourceModel\Membership;

use Magenest\RewardPoints\Api\Data\MembershipCustomerInterface;
use Magenest\RewardPoints\Api\Data\MembershipInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @inheritDoc
     */
    protected $_idFieldName = MembershipInterface::ENTITY_ID;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('Magenest\RewardPoints\Model\Membership', 'Magenest\RewardPoints\Model\ResourceModel\Membership');
    }
}
