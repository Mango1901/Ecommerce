<?php

namespace Magenest\PaymentEPay\Gateway\Command;

use Exception;
use Magenest\PaymentEPay\Logger\Logger;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Sales\Api\InvoiceRepositoryInterface;

/**
 * Class OrderCommand
 * @package Magenest\OnePay\Gateway\Command
 */
class OrderCommand implements CommandInterface
{
    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var HandlerInterface
     */
    private $handler;

    /**
     * @var Logger
     */
    protected $_logger;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var InvoiceRepositoryInterface
     */
    private $invoiceRepository;

    /**
     * OrderCommand constructor.
     *
     * @param ConfigInterface $config
     * @param StoreManagerInterface $storeManager
     * @param RemoteAddress $remoteAddress
     * @param Logger $logger
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param HandlerInterface|null $handler
     */
    public function __construct(
        ConfigInterface $config,
        StoreManagerInterface $storeManager,
        RemoteAddress $remoteAddress,
        Logger $logger,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        InvoiceRepositoryInterface $invoiceRepository,
        HandlerInterface $handler = null
    ) {
        $this->remoteAddress = $remoteAddress;
        $this->config       = $config;
        $this->storeManager = $storeManager;
        $this->handler = $handler;
        $this->_logger = $logger;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->invoiceRepository            = $invoiceRepository;
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
            if(isset($commandSubject['response'])) {
                $response = $commandSubject['response'];
                $payment = $commandSubject['payment'];
                $payment->setTransactionId($response['trxId'])->setIsTransactionClosed(0);
                $payment->setParentTransactionId($response['trxId']);

                $payment
                    ->setAdditionalInformation('merId', $response['merId'])
                    ->setAdditionalInformation('userId', $response['userId'] ?? "N/A")
                    ->setAdditionalInformation('cardNo', $response['cardNo'] ?? "N/A")
                    ->setAdditionalInformation('invoiceNo', $response['invoiceNo'] ?? "N/A")
                    ->setAdditionalInformation('resultMsg', $response['resultMsg'] ?? "N/A")
                    ->setAdditionalInformation('payType', $response['payType'] ?? "N/A")
                    ->setAdditionalInformation('trxId', $response['trxId'] ?? "N/A")
                    ->setAdditionalInformation('bankId', $response['bankId'] ?? "N/A")
                    ->setAdditionalInformation('amountIS', $response['amount'] ?? "N/A");

                $payment->addTransaction(Transaction::TYPE_AUTH);
                if ($this->handler) {
                    $this->handler->handle(
                        $commandSubject,
                        $commandSubject['response']
                    );
                }
                $this->addTransactionIdToInvoice($commandSubject['order'], $response['trxId'], $payment);
            }
        } catch (Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
    }

    /**
     * Function Add Transaction Id To Invoice
     * @param $order
     * @param $transactionId
     * @param Payment $payment
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function addTransactionIdToInvoice($order, $transactionId, $payment = null)
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();

        $searchCriteriaBuilder->addFilter(
            InvoiceInterface::ORDER_ID,
            $order->getId()
        );

        $searchCriteria = $searchCriteriaBuilder
            ->setPageSize(1)
            ->setCurrentPage(1)
            ->create();

        $invoiceList = $this->invoiceRepository->getList($searchCriteria);

        if (count($items = $invoiceList->getItems())) {
            $invoice = current($items);
            $invoice->setOrder($order);
            $invoice->setTransactionId($transactionId);
            $this->invoiceRepository->save($invoice);
        } else {
            // If there is no invoice specified, will automatically prepare an invoice for order
            if ($payment instanceof Payment && $payment->canCapture()) {
                // Capture the payment online
                $payment->capture();
            }
        }
    }
}
