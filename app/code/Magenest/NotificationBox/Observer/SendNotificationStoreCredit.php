<?php

namespace Magenest\NotificationBox\Observer;

use Magenest\NotificationBox\Model\Notification;

class SendNotificationStoreCredit extends SendNotificationPointAbstract
{
    /**
     * @inheritDoc
     */
    protected function getNotificationType()
    {
        return [Notification::STORE_CREDIT_REMINDS];
    }
}
