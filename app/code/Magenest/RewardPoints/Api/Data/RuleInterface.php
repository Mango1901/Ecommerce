<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 13/11/2020 16:59
 */

namespace Magenest\RewardPoints\Api\Data;

interface RuleInterface
{
    const TABLE_NAME = 'magenest_rewardpoints_rule';

    const ENTITY_ID = 'id';

    const RULE_TITLE = 'title';

    const RULE_STATUS = 'status';

    const RULE_STATUS_ACTIVE = '1';

    const RULE_STATUS_INACTIVE = '0';

    const RULE_TYPE = 'rule_type';

    const RULE_DESCRIPTION = 'description';

    const RULE_TYPE_PRODUCT = '1';

    const RULE_TYPE_BEHAVIOR = '2';

    const CUSTOMER_GROUP_IDS = 'customer_group_ids';

    const WEBSITE_IDS = 'web_ids';

    const RULE_ACTION_TYPE = 'action_type';

    const RULE_ACTION_TYPE_GIVE_X = 1;

    const RULE_ACTION_TYPE_SPENT_X_GIVE_Y = 2;

    const RULE_CONDITION_VALUE = 'conditions_serialized';

    const RULE_CONDITION_TYPE = 'condition';

    const RULE_FROM = 'from_date';

    const RULE_TO = 'to_date';

    const POINTS = 'points';

    const STEPS = 'steps';

    const RULE_CONFIGS = 'rule_configs';
}