<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Kangaroo extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Kangaroo
 */

namespace Magenest\OrderManagement\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class InvoiceSaveComment implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * InvoiceSaveComment constructor.
     *
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager
    ) {
        $this->_eventManager = $eventManager;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $observer->getInvoice();
        $order = $invoice->getOrder();
        $this->_eventManager->dispatch(
            "order_management_action_dispatch_save_comment_history", [
            'order' => $order,
            'comment' => __("A invoice is created.")
        ]);
    }
}
