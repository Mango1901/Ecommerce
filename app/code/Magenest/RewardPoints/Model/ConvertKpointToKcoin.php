<?php

namespace Magenest\RewardPoints\Model;

use Magenest\RewardPoints\Helper\Data;
use Magenest\StoreCredit\Api\TransactionRepositoryInterface as TransactionStoreCreditRepositoryInterface;
use Magenest\StoreCredit\Model\Transaction as TransactionStoreCredit;
use Magenest\StoreCredit\Model\TransactionFactory as TransactionStoreCreditFactory;
use Magenest\StoreCredit\Model\ResourceModel\Transaction as TransactionStoreCreditResourceModel;
use Magenest\RewardPoints\Model\Transaction as TransactionRewardPoints;
use Magenest\RewardPoints\Model\TransactionFactory as TransactionRewardPointsFactory;
use Magenest\RewardPoints\Model\ResourceModel\Account as AccountRewardPointsResourceModel;
use Magenest\RewardPoints\Model\ResourceModel\Transaction as TransactionRewardPointsResourceModel;
use Psr\Log\LoggerInterface;

class ConvertKpointToKcoin implements \Magenest\RewardPoints\Api\ConvertKpointToKcoinInterface
{
    const CONVERT_KPOINT_KCOIN_ACTION = 'convert_kpoint_kcoint';

    /**
     * @var TransactionRewardPointsFactory
     */
    protected $transactionRewardPointsFactory;

    /**
     * @var TransactionStoreCreditFactory
     */
    protected $transactionStoreCreditFactory;

    /**
     * @var AccountFactory
     */
    protected $accountFactory;

    /**
     * @var AccountRewardPointsResourceModel
     */
    protected $accountRewardPointsResourceModel;

    /**
     * @var TransactionStoreCreditResourceModel
     */
    protected $transactionStoreCreditResourceModel;

    /**
     * @var TransactionRewardPointsResourceModel
     */
    protected $transactionRewardPointsResourceModel;

    /**
     * @var TransactionStoreCreditRepositoryInterface
     */
    protected $transactionStoreCreditRepository;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param TransactionFactory $transactionRewardPointsFactory
     * @param TransactionStoreCreditFactory $transactionStoreCreditFactory
     * @param AccountFactory $accountFactory
     * @param AccountRewardPointsResourceModel $accountRewardPointsResourceModel
     * @param TransactionStoreCreditResourceModel $transactionStoreCreditResourceModel
     * @param TransactionRewardPointsResourceModel $transactionRewardPointsResourceModel
     * @param TransactionStoreCreditRepositoryInterface $transactionStoreCreditRepository
     * @param Data $dataHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        TransactionRewardPointsFactory $transactionRewardPointsFactory,
        TransactionStoreCreditFactory $transactionStoreCreditFactory,
        AccountFactory $accountFactory,
        AccountRewardPointsResourceModel $accountRewardPointsResourceModel,
        TransactionStoreCreditResourceModel $transactionStoreCreditResourceModel,
        TransactionRewardPointsResourceModel $transactionRewardPointsResourceModel,
        TransactionStoreCreditRepositoryInterface $transactionStoreCreditRepository,
        Data $dataHelper,
        LoggerInterface $logger
    ) {
        $this->transactionRewardPointsFactory = $transactionRewardPointsFactory;
        $this->transactionStoreCreditFactory = $transactionStoreCreditFactory;
        $this->accountFactory = $accountFactory;
        $this->accountRewardPointsResourceModel = $accountRewardPointsResourceModel;
        $this->transactionStoreCreditResourceModel = $transactionStoreCreditResourceModel;
        $this->transactionRewardPointsResourceModel = $transactionRewardPointsResourceModel;
        $this->transactionStoreCreditRepository = $transactionStoreCreditRepository;
        $this->dataHelper = $dataHelper;
        $this->logger = $logger;
    }

    /**
     * @inheirtDoc
     */
    public function execute($customerId, $kpoint)
    {
        try {
            $accountModel = $this->accountFactory->create();
            $this->accountRewardPointsResourceModel->load($accountModel, $customerId, 'customer_id');

            if ($accountModel->getId()) {
                $currentPoint = $accountModel->getData('points_current');
                if ($currentPoint >= $kpoint) {
                    // Change information of Point Spent and Point current of Customer
                    $data = [
                        'customer_id' => $customerId,
                        'points_spent' => $accountModel->getData('points_spent') + $kpoint,
                        'points_current' => $currentPoint - $kpoint,
                    ];
                    $accountModel->addData($data);
                    $this->accountRewardPointsResourceModel->save($accountModel);

                    // Create Transaction Of Minus point for Kpoint - Reward Point
                    /**
                     * @var TransactionRewardPoints $transactionModel
                     */
                    $transactionModel = $this->transactionRewardPointsFactory->create();
                    $comment = 'Convert Kpoint To Kcoint';
                    $data = [
                        'customer_id' => $customerId,
                        'points_change' => -$kpoint,
                        'points_after' => $accountModel->getData('points_current'),
                        'comment' => $comment,
                        'rule_id' => -6
                    ];
                    $transactionModel->addData($data);
                    $this->transactionRewardPointsResourceModel->save($transactionModel);

                    // Calculate kcoin from kpoint by rate in configuration
                    $rateConvert = $this->dataHelper->getRateConvertKpoint();
                    $kcoin = $kpoint * $rateConvert;

                    // Create Transaction Of Plus coin for Kcoin - Store Credit
                    /** @var TransactionStoreCredit $transactionStoreCredit */
                    $transactionStoreCredit = $this->transactionStoreCreditFactory->create();
                    $transactionStoreCredit->setAction(self::CONVERT_KPOINT_KCOIN_ACTION);
                    $transactionStoreCredit->setCustomerId($customerId);
                    $transactionStoreCredit->setAmount($kcoin);
                    $this->transactionStoreCreditRepository->createTransactionByAction($transactionStoreCredit, self::CONVERT_KPOINT_KCOIN_ACTION);
                    return true;
                }
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return false;
    }
}
