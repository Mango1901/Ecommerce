<?php
namespace Magenest\OrderCancel\Model;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\CreditmemoManagementInterfaceFactory;
use Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\CreditmemoSender;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection;
use Psr\Log\LoggerInterface;

class CreditmemoCreator
{
    /** @var CreditmemoLoader */
    private $creditmemoLoader;

    /** @var CreditmemoSender */
    private $creditmemoSender;

    /** @var CreditmemoManagementInterfaceFactory */
    private $creditmemoManagement;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param CreditmemoLoader $creditmemoLoader
     * @param CreditmemoSender $creditmemoSender
     * @param CreditmemoManagementInterfaceFactory $creditmemoManagement
     * @param LoggerInterface $logger
     */
    public function __construct(
        CreditmemoLoader                     $creditmemoLoader,
        CreditmemoSender                     $creditmemoSender,
        CreditmemoManagementInterfaceFactory $creditmemoManagement,
        LoggerInterface                      $logger
    ) {
        $this->creditmemoManagement = $creditmemoManagement;
        $this->logger               = $logger;
        $this->creditmemoLoader     = $creditmemoLoader;
        $this->creditmemoSender     = $creditmemoSender;
    }

    /**
     * @param Order $order
     * @param $reason
     * @param Collection $invoiceCollection
     *
     * @throws LocalizedException
     */
    public function start($order, $reason, $invoiceCollection)
    {
        $creditmemo = [
            'do_offline'          => !(bool)$invoiceCollection->getSize() ? '1' : '0',
            'comment_text'        => $reason,
            'shipping_amount'     => $order->getBaseShippingAmount(),
            'adjustment_positive' => 0,
            'adjustment_negative' => 0
        ];
        $items      = [];
        foreach ($order->getAllItems() as $item) {
            if ($item->getProductType() == Configurable::TYPE_CODE) {
                continue;
            }
            if ($item->getParentItemId()) {
                $items[$item->getParentItemId()]['qty']           = $item->getQtyOrdered();
                $items[$item->getParentItemId()]['back_to_stock'] = '1';
                continue;
            }
            $items[$item->getId()]['qty']           = (int)$item->getQtyOrdered();
            $items[$item->getId()]['back_to_stock'] = '1';
        }
        $creditmemo['items'] = $items;
        $invoiceId           = $invoiceCollection->getSize() === 1 ? $invoiceCollection->getFirstItem()->getId() : null;

        $this->create($order->getId(), $creditmemo, $invoiceId);
    }

    /**
     * @param $orderId
     * @param $creditmemo
     *
     * @throws LocalizedException
     */
    protected function create($orderId, $creditmemo, $invoiceId = null)
    {
        $this->creditmemoLoader->setInvoiceId($invoiceId);
        $this->creditmemoLoader->setOrderId($orderId);
        $this->creditmemoLoader->setCreditmemo($creditmemo);
        $creditmemo = $this->creditmemoLoader->load();
        if ($creditmemo) {
            if (!$creditmemo->isValidGrandTotal()) {
                throw new LocalizedException(
                    __('The credit memo\'s total must be positive.')
                );
            }

            if (!empty($creditmemo['comment_text'])) {
                $creditmemo->addComment(
                    $creditmemo['comment_text'],
                    isset($creditmemo['comment_customer_notify']),
                    isset($creditmemo['is_visible_on_front'])
                );

                $creditmemo->setCustomerNote($creditmemo['comment_text']);
                $creditmemo->setCustomerNoteNotify(isset($creditmemo['comment_customer_notify']));
            }

            if (isset($creditmemo['do_offline'])) {
                //do not allow online refund for Refund to Store Credit
                if (!$creditmemo['do_offline'] && !empty($creditmemo['refund_customerbalance_return_enable'])) {
                    throw new LocalizedException(__('Cannot create online refund for Refund to Store Credit.'));
                }
            }
            $creditmemoManagement = $this->creditmemoManagement->create();
            $creditmemo->getOrder()->setCustomerNoteNotify(!empty($creditmemo['send_email']));
            $doOffline = isset($creditmemo['do_offline']) && (bool)$creditmemo['do_offline'];
            $creditmemoManagement->refund($creditmemo, $doOffline);

            try {
                if (!empty($creditmemo['send_email'])) {
                    $this->creditmemoSender->send($creditmemo);
                }
            } catch (\Exception $e) {
                $this->logger->debug($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            }
        }
    }
}
