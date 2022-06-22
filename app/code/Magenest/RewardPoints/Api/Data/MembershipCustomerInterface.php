<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 06/11/2020 10:58
 */

namespace Magenest\RewardPoints\Api\Data;

interface MembershipCustomerInterface
{
    const TABLE_NAME = 'magenest_rewardpoints_membership_customer';

    const CUSTOMER_ID = 'customer_id';

    const MEMBERSHIP_ID = 'membership_id';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';
}