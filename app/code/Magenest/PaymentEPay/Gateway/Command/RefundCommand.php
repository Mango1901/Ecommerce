<?php

namespace Magenest\PaymentEPay\Gateway\Command;

use Exception;
use Magenest\PaymentEPay\Api\Data\HandlePaymentInterface;
use Magenest\PaymentEPay\Helper\Data;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class CompleteCommand
 * @package Magenest\OnePay\Gateway\Command
 */
class RefundCommand implements CommandInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var InvoiceSender
     */
    private $invoiceSender;

    /** @var OrderSender  */
    private $orderSender;

    /**
     * @var InvoiceService
     */
    private $invoiceService;

    /**
     * @var \Magento\Checkout\Model\Session $checkoutSession
     */
    protected $checkoutSession;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var MessageManagerInterface
     */
    protected $messageManager;

    protected $_logger;

    /**
     * @var HandlePaymentInterface
     */
    private $handlePaymentInterface;

    /**
     * CompleteCommand constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param InvoiceSender $invoiceSender
     * @param InvoiceService $invoiceService
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        InvoiceSender $invoiceSender,
        OrderSender $orderSender,
        InvoiceService $invoiceService,
        MessageManagerInterface $messageManager,
        Data $helperData,
        \Magenest\PaymentEPay\Logger\Logger $logger,
        \Magento\Checkout\Model\Session $checkoutSession,
        HandlePaymentInterface $handlePaymentInterface
    ) {
        $this->storeManager   = $storeManager;
        $this->invoiceSender  = $invoiceSender;
        $this->orderSender  = $orderSender;
        $this->invoiceService = $invoiceService;
        $this->checkoutSession = $checkoutSession;
        $this->helperData = $helperData;
        $this->messageManager = $messageManager;
        $this->_logger = $logger;
        $this->handlePaymentInterface = $handlePaymentInterface;
    }

    /**
     * @param array $commandSubject
     *
     * @return \Magento\Payment\Gateway\Command\ResultInterface|null|void
     * @throws Exception
     */
    public function execute(array $commandSubject)
    {
        try {
            $paymentDataObj = SubjectReader::readPayment($commandSubject);
            $payment = $paymentDataObj->getPayment();
            $creditMemo = $payment->getCreditmemo();
            $orderIncrementId = $paymentDataObj->getOrder()->getOrderIncrementId();
            $trxId = $payment->getAdditionalInformation('trxId');
            $refundAmount = floatval($creditMemo->getBaseGrandTotal());
            $payType = $payment->getAdditionalInformation('payType');
            $cancelMsg = '';
            if ($payType == "IS") {
                $cancelResultIS = $this->handlePaymentInterface->handleCancelTransIS($trxId, $payType, $type = "2", $cancelMsg);
                $resultToJson = json_decode($cancelResultIS, true);
            } else {
                $cancelResult = $this->handlePaymentInterface->handleCancelTrans($trxId, $refundAmount, $payType, $cancelMsg);
                $resultToJson = json_decode($cancelResult, true);
            }

            if ($resultToJson['resultCd'] == '00_000') {
                $checkToken = $this->helperData->checkToken($resultToJson);
                if ($checkToken) {
                    $payment->setAdditionalInformation($resultToJson);
                    $payment->save();
                    $this->_logger->info("\n======================================\n");
                    $this->_logger->info('-----------RESULT REFUND' . $orderIncrementId . ':' . $cancelResult);
                } else {
                    throw new Exception(__("Invalid Merchant Token."));
                }
            } else {
                $this->_logger->info("\n======================================\n");
                $this->_logger->info('-----------RESULT REFUND FAIL' . $orderIncrementId . ':' . $cancelResult);
                $message = __($resultToJson['resultMsg']);
                throw new Exception($message->getText());
            }
        } catch (LocalizedException $exception) {
            $this->_logger->debug("--- exception refund: " . $exception->getMessage() . " ---");
            throw new LocalizedException($exception->getMessage());
        } catch (Exception $exception) {
            $this->_logger->debug("--- exception refund: " . $exception->getMessage() . " ---");
            throw new Exception($exception->getMessage());
        }
    }
}
