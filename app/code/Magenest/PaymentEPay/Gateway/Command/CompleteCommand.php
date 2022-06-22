<?php

namespace Magenest\PaymentEPay\Gateway\Command;

use Magenest\PaymentEPay\Setup\Patch\Data\AddPendingPaidOrderStatus;
use Magenest\PaymentEPay\Setup\Patch\Data\UpdatePendingPaidOrderStatus;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Command\ResultInterface;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Framework\DB\TransactionFactory;

/**
 * Class CompleteCommand
 * @package Magenest\OnePay\Gateway\Command
 */
class CompleteCommand implements CommandInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var InvoiceSender
     */
    private $invoiceSender;

    /** @var OrderSender */
    private $orderSender;

    /**
     * @var InvoiceService
     */
    private $invoiceService;

    /**
     * @var Session $checkoutSession
     */
    protected $checkoutSession;

    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;

    /**
     * CompleteCommand constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param InvoiceSender $invoiceSender
     * @param OrderSender $orderSender
     * @param InvoiceService $invoiceService
     * @param Session $checkoutSession
     * @param TransactionFactory $transaction
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        InvoiceSender         $invoiceSender,
        OrderSender           $orderSender,
        InvoiceService        $invoiceService,
        Session               $checkoutSession,
        TransactionFactory    $transactionFactory
    ) {
        $this->storeManager = $storeManager;
        $this->invoiceSender = $invoiceSender;
        $this->orderSender = $orderSender;
        $this->invoiceService = $invoiceService;
        $this->checkoutSession = $checkoutSession;
        $this->transactionFactory = $transactionFactory;
    }

    /**
     * @param array $commandSubject
     *
     * @return ResultInterface|null|void
     * @throws LocalizedException
     */
    public function execute(array $commandSubject)
    {
        /** @var Order $order */
        $order = $commandSubject['order'] ?? null;

        if (!$order || !$order->getId()) {
            throw new LocalizedException(__('The order no longer exists.'));
        }
        $order->setStatus(UpdatePendingPaidOrderStatus::STATUS_CODE);

        $transaction = $this->transactionFactory->create();
        $transaction->addObject($order)->save();

        $this->checkoutSession->setForceOrderMailSentOnSuccess(true);
        $this->orderSender->send($order, true);
    }
}
