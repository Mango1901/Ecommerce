<?php

namespace Magenest\StoreCredit\Observer;

use Magenest\StoreCredit\Api\TransactionRepositoryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magenest\StoreCredit\Api\Data\TransactionInterface;
use Magenest\StoreCredit\Api\Data\TransactionInterfaceFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Registry;

class CreateStoreCreditTransactionFromAffiliate implements ObserverInterface
{
    const PREFIX_ACTION = 'affiliate_';
    const IGNORE_EVENT_TRANSACTION_STORE_CREDIT = 'ignore_event_store_credit';

    /**
     * @var TransactionRepositoryInterface
     */
    protected $transactionRepository;

    /**
     * @var TransactionInterfaceFactory
     */
    protected $transactionInterfaceFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @param TransactionRepositoryInterface $transactionRepository
     * @param TransactionInterfaceFactory $transactionInterfaceFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        TransactionRepositoryInterface $transactionRepository,
        TransactionInterfaceFactory $transactionInterfaceFactory,
        LoggerInterface $logger,
        Registry $registry
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->transactionInterfaceFactory = $transactionInterfaceFactory;
        $this->logger = $logger;
        $this->registry = $registry;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        try {
            $this->registry->register(self::IGNORE_EVENT_TRANSACTION_STORE_CREDIT, true);
            /**
             * @var \Magenest\Affiliate\Model\Transaction $affiliateTransaction
             */
            $affiliateTransaction = $observer->getEvent()->getDataObject();
            /**
             * @var TransactionInterface $storeCreditTransaction
             */
            $storeCreditTransaction = $this->transactionInterfaceFactory->create();

            $actionName = self::PREFIX_ACTION . preg_replace('/\//i', '_', $affiliateTransaction->getAction());
            $storeCreditTransaction->setAction($actionName);
            $storeCreditTransaction->setCustomerId($affiliateTransaction->getCustomerId());
            $storeCreditTransaction->setAmount($affiliateTransaction->getAmount());
            $this->transactionRepository->createTransactionByAction($storeCreditTransaction, $actionName);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }
}
