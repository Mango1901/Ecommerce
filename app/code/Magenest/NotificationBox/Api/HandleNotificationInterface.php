<?php
namespace Magenest\NotificationBox\Api;

/**
 * Interface HandleNotificationInterface
 * @package Magenest\NotificationBox\Api
 */
interface HandleNotificationInterface {

    /** delete notifications
     * @param int $customerId
     * @param string $notificationId
     * @return array
     */
    public function deleteNotifications($customerId,$notificationId);

    /** mark as read notifications
     * @param int $customerId
     * @param string $notificationId
     * @return array
     */
    public function markAsRead($customerId,$notificationId);


    /** mark important notifications
     * @param int $status
     * @param int $customerId
     * @param string $notificationId
     * @return array
     */
    public function markImportant($customerId,$notificationId,$status);

}
