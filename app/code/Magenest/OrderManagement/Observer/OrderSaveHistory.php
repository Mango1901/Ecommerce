<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderManagement\Observer;

use Magento\Sales\Model\Order;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Backend\Model\Auth\Session as AuthSession;

/**
 * Class Magenest\OrderManagement\Observer
 * @package OrderSaveHistor
 */
class OrderSaveHistory implements ObserverInterface
{
    /**
     * @var AuthSession
     */
    protected $_authSession;

    /**
     * Constructor.
     *
     * @param AuthSession $_authSession
     */
    public function __construct(AuthSession $_authSession)
    {
        $this->_authSession = $_authSession;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getOrder();
        $comment = $observer->getComment();

        if ($order && $order instanceof Order) {
            if ($this->_authSession->getUser()) {
                $comment .= " " . __("Commented by %1", $this->_authSession->getUser()->getName());
            }

            $order->addCommentToStatusHistory($comment, $order->getStatus())
                ->setIsVisibleOnFront(false)
                ->setIsCustomerNotified(false)
                ->save();
        }
    }
}
