<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 06/11/2020 14:58
 */

namespace Magenest\RewardPoints\Api\Data;

interface NotificationInterface
{
    const TABLE_NAME = 'magenest_rewardpoints_notification';

    const ENTITY_ID = 'entity_id';

    const RULE_ID = 'rule_id';

    const NOTIFICATION_STATUS = 'notification_status';

    const NOTIFICATION_STATUS_ENABLE = 1;

    const NOTIFICATION_STATUS_DISABLE = 0;

    const NOTIFICATION_CONTENT = 'notification_content';

    const NOTIFICATION_DISPLAY_POSITION = 'notification_display_position';

    const NOTIFICATION_DISPLAY_POSITION_CUSTOMER = 1;

    const NOTIFICATION_DISPLAY_POSITION_CART = 2;

    const NOTIFICATION_DISPLAY_FOR_GUEST = 'notification_display_for_guest';
    const NOTIFICATION_DISPLAY_FOR_GUEST_YES = 1;
    const NOTIFICATION_DISPLAY_FOR_GUEST_NO = 0;
}