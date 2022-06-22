<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 06/11/2020 11:11
 */

namespace Magenest\RewardPoints\Model\ResourceModel\MembershipCustomer;

use Magenest\RewardPoints\Api\Data\MembershipCustomerInterface;
use Magenest\RewardPoints\Api\Data\MembershipInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @inheritDoc
     */
    protected $_idFieldName = MembershipCustomerInterface::CUSTOMER_ID;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('Magenest\RewardPoints\Model\MembershipCustomer', 'Magenest\RewardPoints\Model\ResourceModel\MembershipCustomer');
    }

    public function joinMembership()
    {
        $this->getSelect()->joinLeft(['membership' => $this->getTable(MembershipInterface::TABLE_NAME)],
                'main_table.' . MembershipCustomerInterface::MEMBERSHIP_ID . ' = membership.' . MembershipInterface::ENTITY_ID
            );
        return $this;
    }
}