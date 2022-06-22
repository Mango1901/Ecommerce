<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 06/11/2020 10:59
 */

namespace Magenest\RewardPoints\Model;

use Magenest\RewardPoints\Api\Data\MembershipCustomerInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class MembershipCustomer extends AbstractModel implements IdentityInterface
{
    /**
     * @var string
     */
    protected $_eventPrefix = MembershipCustomerInterface::TABLE_NAME;

    const CACHE_TAG = 'magenest_rewardpoints_membership_customer';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('Magenest\RewardPoints\Model\ResourceModel\MembershipCustomer');
    }

    /**
     * @inheritDoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}