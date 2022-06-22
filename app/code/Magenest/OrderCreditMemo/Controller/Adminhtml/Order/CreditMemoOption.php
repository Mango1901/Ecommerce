<?php

namespace Magenest\OrderCreditMemo\Controller\Adminhtml\Order;

use Magenest\OrderCreditMemo\Plugin\Adminhtml\ViewOrder;
use Magento\Sales\Controller\Adminhtml\Order;

class CreditMemoOption extends Order
{
    const ADMIN_RESOURCE = 'Magento_Sales::cancel';

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->_initOrder();

        $refundOption = $this->getRequest()->getParam('refund_option');

        if ($refundOption == ViewOrder::REFUND_ONLINE) {
            $invoiceId = $order->getInvoiceCollection()->getFirstItem()->getId() ?? null;
            return $resultRedirect->setPath(
                'sales/order_creditmemo/start',
                [
                    'order_id' => $order->getId(),
                    'invoice_id' => $invoiceId
                ]
            );
        } elseif ($refundOption == ViewOrder::REFUND_OFFLINE) {
            return $resultRedirect->setPath(
                'sales/order_creditmemo/start',
                [
                    'order_id' => $order->getId()
                ]
            );
        }

        return $resultRedirect->setPath('sales/*/');
    }
}
