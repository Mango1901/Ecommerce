<?php

namespace Magenest\NotificationBox\Observer;

use Magenest\NotificationBox\Model\Notification;

class SendNotificationRewardPoint extends SendNotificationPointAbstract
{
    /**
     * @inheritDoc
     */
    protected function getNotificationType()
    {
        return [Notification::REWARD_POINT_REMINDS];
    }
}
