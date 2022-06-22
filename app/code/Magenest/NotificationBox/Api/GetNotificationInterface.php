<?php
namespace Magenest\NotificationBox\Api;

/**
 * Interface GetNotificationInterface
 * @package Magenest\NotificationBox\Api
 */
interface GetNotificationInterface {
    /**
     * @param int $customerId
     * @return array
     */
    public function getCustomerNotification($customerId);
}
