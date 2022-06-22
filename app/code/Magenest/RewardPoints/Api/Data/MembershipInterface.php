<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 29/10/2020 14:12
 */

namespace Magenest\RewardPoints\Api\Data;

interface MembershipInterface
{
    const TABLE_NAME = 'magenest_rewardpoints_membership';

    const ENTITY_ID = 'id';

    const GROUP_NAME = 'name';

    const GROUP_CODE = 'code';

    const GROUP_DESCRIPTION = 'description';

    const GROUP_BENEFIT = 'benefit';

    const GROUP_REQUIREMENTS = 'requirements';

    const SORT_ORDER = 'sort_order';

    const GROUP_STATUS = 'is_active';

    const GROUP_STATUS_ENABLE = 1;

    const GROUP_STATUS_DISABLE = 0;

    const GROUP_CONDITION_TYPE = 'condition_reach_tier';

    const GROUP_CONDITION_TYPE_DEFAULT = 0;

    const GROUP_CONDITION_TYPE_BY_POINT_NUMBER = 1;

    const GROUP_CONDITION_TYPE_BY_SPEND_POINT = 2;

    const GROUP_CONDITION_VALUE = 'condition_reach_tier_value';

    const TIER_LOGO = 'tier_logo';

    const ADDITIONAL_EARNING_POINT = 'additional_earning_point';

    const ADDED_VALUE_TYPE = 'added_value_type';

    const ADDED_VALUE_DESCRIPTION = 'added_value_description';

    const ADDED_VALUE_TYPE_FIXED = 1;

    const ADDED_VALUE_TYPE_PERCENT = 2;

    const ADDED_VALUE_AMOUNT = 'added_value_amount';

    const CREATE_AT = 'create_at';
}