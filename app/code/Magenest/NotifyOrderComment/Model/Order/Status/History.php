<?php

namespace Magenest\NotifyOrderComment\Model\Order\Status;

use Magenest\NotifyOrderComment\Api\Data\OrderStatusHistoryInterface;

class History extends \Magento\Sales\Model\Order\Status\History implements OrderStatusHistoryInterface
{
    /**
     * @inheritDoc
     */
    public function getUserId()
    {
        return $this->getData(OrderStatusHistoryInterface::USER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setUserId($userId)
    {
        return $this->setData(OrderStatusHistoryInterface::USER_ID, $userId);
    }
}
