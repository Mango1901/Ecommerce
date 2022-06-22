<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 06/11/2020 11:09
 */

namespace Magenest\RewardPoints\Model\ResourceModel;

use Magenest\RewardPoints\Api\Data\MembershipCustomerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class MembershipCustomer extends AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(MembershipCustomerInterface::TABLE_NAME, MembershipCustomerInterface::CUSTOMER_ID);
        $this->_isPkAutoIncrement = false;
    }

    /**
     * @param $membershipIds
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function removeAllCustomersInGroup($membershipIds)
    {
        if (!empty($membershipIds)) {
            if (!is_array($membershipIds)) {
                $membershipIds = [$membershipIds];
            }
            $this->getConnection()->delete($this->getMainTable(), new \Zend_Db_Expr(MembershipCustomerInterface::MEMBERSHIP_ID . ' IN (' . implode(',', $membershipIds) . ')'));
        }

        return $this;
    }

    /**
     * @param $membershipId
     * @param $listCustomers
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function assignCustomerToTier($membershipId, $listCustomers)
    {
        $this->removeAllCustomersInGroup($membershipId);
        if (!empty($listCustomers)) {
            $this->getConnection()->insertArray($this->getMainTable(), array_keys(reset($listCustomers)), $listCustomers);
        }

        return $this;
    }
}