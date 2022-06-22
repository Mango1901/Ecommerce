<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 06/11/2020 10:25
 */

namespace Magenest\RewardPoints\Api\Data;

interface AccountInterface
{
    const TABLE_NAME = 'magenest_rewardpoints_account';

    const ENTITY_ID = 'id';

    const CUSTOMER_ID = 'customer_id';

    const POINTS_TOTAL = 'points_total';

    const POINTS_CURRENT = 'points_current';

    const POINTS_SPENT = 'points_spent';

    const LOYALTY_ID = 'loyalty_id';

    const STORE_ID = 'store_id';
}