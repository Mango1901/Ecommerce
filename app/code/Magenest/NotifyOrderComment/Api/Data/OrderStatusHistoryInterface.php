<?php

namespace Magenest\NotifyOrderComment\Api\Data;

interface OrderStatusHistoryInterface extends \Magento\Sales\Api\Data\OrderStatusHistoryInterface
{
    const USER_ID = 'user_id';

    /**
     * Gets the creator id for the order status history.
     *
     * @return int|null Entity name.
     */
    public function getUserId();

    /**
     * Sets the creator id for the order status history.
     *
     * @param int $userId
     * @return $this
     */
    public function setUserId($userId);
}
