<?php

namespace Magenest\Affiliate\Observer;

use Magenest\Affiliate\Helper\Data;
use Magenest\AffiliateOpt\Api\Data\TransactionInterface;
use Magenest\AffiliateOpt\Api\Data\TransactionInterfaceFactory;
use Magenest\AffiliateOpt\Api\TransactionRepositoryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;

class CreateAffiliateTransactionFromStoreCredit implements ObserverInterface
{
    const PREFIX_ACTION = 'store_credit/';
    const IGNORE_EVENT_TRANSACTION_AFFILIATE = 'ignore_event_affiliate';

    /**
     * @var TransactionRepositoryInterface
     */
    protected $transactionRepository;

    /**
     * @var TransactionInterfaceFactory
     */
    protected $transactionInterfaceFactory;

    /**
     * @var Data
     */
    protected $data;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * StoreCreditConvertData constructor.
     *
     * @param TransactionRepositoryInterface $transactionRepository
     * @param TransactionInterfaceFactory $transactionInterfaceFactory
     * @param Data $data
     * @param LoggerInterface $logger
     * @param Registry $registry
     */
    public function __construct(
        TransactionRepositoryInterface $transactionRepository,
        TransactionInterfaceFactory    $transactionInterfaceFactory,
        Data                           $data,
        LoggerInterface                $logger,
        Registry                       $registry
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->transactionInterfaceFactory = $transactionInterfaceFactory;
        $this->data = $data;
        $this->logger = $logger;
        $this->registry = $registry;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        try {
            $this->registry->register(self::IGNORE_EVENT_TRANSACTION_AFFILIATE, true);
            /**
             * @var $transactionStoreCredit \Magenest\StoreCredit\Model\Transaction
             */
            $transactionStoreCredit = $observer->getEvent()->getDataObject();

            /** Add spending affiliate transaction if current account is affiliate */
            if ($accountId = $this->data->getAccountIdByCustomerId($transactionStoreCredit->getCustomerId())) {
                /**
                 * @var $transactionModel TransactionInterface
                 */
                $transactionModel = $this->transactionInterfaceFactory->create();
                $transactionModel->setAccountId($accountId);
                $transactionModel->setAmount($transactionStoreCredit->getAmount());
                $transactionModel->setTitle($transactionStoreCredit->getTitle());
                $this->transactionRepository->createTransactionByAction(
                    $transactionModel,
                    self::PREFIX_ACTION . $transactionStoreCredit->getAction()
                );
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }
}
