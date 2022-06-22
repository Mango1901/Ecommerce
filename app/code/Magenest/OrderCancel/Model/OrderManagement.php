<?php

namespace Magenest\OrderCancel\Model;

use Magenest\OrderCancel\Api\SendSmsCancelOrderInterface;
use Magenest\OrderCancel\Block\Order\Info\ReasonCancel;
use Magenest\OrderCancel\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Registry;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\OrderCommentSender;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\SalesRule\Model\Coupon\UpdateCouponUsages;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

class OrderManagement
{
    const AUTOMATICALLY_SEND_EMAIL_CONFIG = "sales_email/order/cancel_email";
    const REASON_CANCEL_ORDER_VAR_TEMPLATE = 'reason_cancel_order_var_template';
    const VALID_SCOPES = [
        ScopeInterface::SCOPE_STORE,
        ScopeInterface::SCOPE_STORES,
        ScopeInterface::SCOPE_WEBSITE,
        ScopeInterface::SCOPE_WEBSITES
    ];

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var ManagerInterface */
    private $eventManager;

    /** @var CreditmemoCreator */
    private $creditmemoCreator;

    /** @var OrderCommentSender */
    private $orderCommentSender;

    /** @var UpdateCouponUsages */
    private $updateCouponUsages;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var InvoiceRepositoryInterface */
    private $invoiceRepository;

    /** @var LoggerInterface */
    private $logger;

    /** @var SendSmsCancelOrderInterface */
    private $sendSmsCancelOrder;

    /** @var Data */
    private $helperData;

    /** @var Registry */
    protected $registry;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param OrderRepositoryInterface $orderRepository
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param ManagerInterface $eventManager
     * @param LoggerInterface $logger
     * @param CreditmemoCreator $creditmemoCreator
     * @param OrderCommentSender $orderCommentSender
     * @param UpdateCouponUsages $updateCouponUsages
     * @param SendSmsCancelOrderInterface $sendSmsCancelOrder
     * @param Data $helperData
     * @param Registry $registry
     */
    public function __construct(
        ScopeConfigInterface        $scopeConfig,
        OrderRepositoryInterface    $orderRepository,
        InvoiceRepositoryInterface  $invoiceRepository,
        ManagerInterface            $eventManager,
        LoggerInterface             $logger,
        CreditmemoCreator           $creditmemoCreator,
        OrderCommentSender          $orderCommentSender,
        UpdateCouponUsages          $updateCouponUsages,
        SendSmsCancelOrderInterface $sendSmsCancelOrder,
        Data                        $helperData,
        Registry                    $registry
    )
    {
        $this->logger = $logger;
        $this->orderCommentSender = $orderCommentSender;
        $this->creditmemoCreator = $creditmemoCreator;
        $this->scopeConfig = $scopeConfig;
        $this->orderRepository = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->eventManager = $eventManager;
        $this->updateCouponUsages = $updateCouponUsages;
        $this->sendSmsCancelOrder = $sendSmsCancelOrder;
        $this->helperData = $helperData;
        $this->registry = $registry;
    }

    /**
     * @param Order $order
     * @param string $reason
     * @param bool $sendMail
     *
     * @return false
     * @throws \Exception
     */
    public function cancelOrder(Order $order, $reason = null, $sendMail = false)
    {
        $this->registry->register(self::REASON_CANCEL_ORDER_VAR_TEMPLATE, $reason);
        if ($order->canCancel()) {
            $order->cancel();
            try {
                $this->updateCouponUsages->execute($order, false);
            } catch (\Exception $e) {
                $this->logger->debug($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            }

            $this->orderRepository->save($order);
        } elseif ($order->hasInvoices() && !$order->hasShipments() && !$order->canCreditmemo()) {
            $originOrder = $this->orderRepository->get($order->getId());
            if ($originOrder->getInvoiceCollection()->getSize()) {
                /** @var Invoice $invoice */
                foreach ($originOrder->getInvoiceCollection() as $invoice) {
                    $invoice->setState(Invoice::STATE_OPEN); // Force cancel invoice
                    if ($invoice->canCancel()) {
                        $invoice->cancel();
                        $this->invoiceRepository->save($invoice);
                    }
                }
            }
            $originOrder->cancel();
            $order = $this->orderRepository->save($originOrder);
        } else {
            $isCreditmemo = true;
            $this->creditmemoCreator->start($order, $reason, $order->getInvoiceCollection());
            // Update current state of product when credit memo
            $order = $this->orderRepository->get($order->getId());
        }
        if ($reason && !isset($isCreditmemo)) {
            $this->eventManager->dispatch(
                "order_management_action_dispatch_save_comment_history",
                [
                    'order' => $order,
                    'comment' => __(ReasonCancel::PREFIX_REASON_ENGLISH . " %1", $reason)
                ]
            );
        }
        if ($sendMail && $this->scopeConfig->getValue(self::AUTOMATICALLY_SEND_EMAIL_CONFIG)) {
            $message = __("Your order has been cancelled.");
            $message = isset($reason) && $reason ? $reason : $message;
            $this->orderCommentSender->send($order, true, $message);
        }

        $messageSendSms = sprintf($this->helperData->getMessage(), $order->getId(), $reason);

        if ($this->helperData->isEnabledSendSmsCancelOrder()) {
            $sendSmsResult = $this->sendSmsCancelOrder->sendSingleSms(
                $messageSendSms,
                $this->helperData->getPhone()
            );

            if ($sendSmsResult) {
                $messageLog = __('Send SMS Admin Success With Text: ') . $messageSendSms;
            } else {
                $messageLog = __('Send SMS Admin Failed With Text: ') . $messageSendSms;
            }

            $order->addCommentToStatusHistory(
                $messageLog,
                false,
                false
            )->setIsCustomerNotified(false);

            $this->orderRepository->save($order);
        }
        return false;
    }
}
