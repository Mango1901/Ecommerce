<?php

namespace Magenest\AffiliateOpt\Model;

use Exception;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magenest\Affiliate\Model\AccountFactory;
use Magenest\Affiliate\Model\TransactionFactory;
use Magenest\AffiliateOpt\Api\Data\TransactionInterface;
use Magenest\AffiliateOpt\Api\Data\TransactionSearchResultInterface;
use Magenest\AffiliateOpt\Api\Data\TransactionSearchResultInterfaceFactory as SearchResultFactory;
use Magenest\AffiliateOpt\Api\TransactionRepositoryInterface;
use Magenest\AffiliateOpt\Helper\Data;
use Magenest\AffiliateOpt\Model\TransactionFactory as TransactionAPIFactory;

/**
 * Class TransactionRepository
 * @package Magenest\AffiliateOpt\Model
 */
class TransactionRepository implements TransactionRepositoryInterface
{
    /**
     * @var \Magenest\AffiliateOpt\Model\TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var TransactionFactory
     */
    protected $transactionAPIFactory;

    /**
     * @var AccountFactory
     */
    protected $_accountFactory;

    /**
     * @var SearchResultFactory
     */
    protected $searchResultFactory = null;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * TransactionRepository constructor.
     *
     * @param Data $helperData
     * @param TransactionFactory $transactionFactory
     * @param AccountFactory $accountFactory
     * @param \Magenest\AffiliateOpt\Model\TransactionFactory $transactionAPIFactory
     * @param SearchResultFactory $searchResultFactory
     */
    public function __construct(
        Data $helperData,
        TransactionFactory $transactionFactory,
        AccountFactory $accountFactory,
        TransactionAPIFactory $transactionAPIFactory,
        SearchResultFactory $searchResultFactory
    ) {
        $this->helperData = $helperData;
        $this->transactionFactory = $transactionFactory;
        $this->_accountFactory = $accountFactory;
        $this->transactionAPIFactory = $transactionAPIFactory;
        $this->searchResultFactory = $searchResultFactory;
    }

    /**
     * Find entities by criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return TransactionSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResult = $this->searchResultFactory->create();

        return $this->helperData->processGetList($searchCriteria, $searchResult);
    }

    /**
     * {@inheritDoc}
     */
    public function getTransactionByAffiliateId($id)
    {
        if (!$id) {
            throw new InputException(__('Affiliate id required'));
        }

        return $this->searchResultFactory->create()->addFieldToFilter('account_id', $id);
    }

    /**
     * {@inheritDoc}
     */
    public function getTransactionByOrderId($id)
    {
        if (!$id) {
            throw new InputException(__('Order id required'));
        }

        return $this->searchResultFactory->create()->addFieldToFilter('order_id', $id);
    }

    /**
     * {@inheritDoc}
     */
    public function save(TransactionInterface $data)
    {
        if ($data->getAmount() <= 0.001) {
            throw new LocalizedException(__('Transaction amount must great than zero!'));
        }

        $affiliateAccount = $this->_accountFactory->create()->load($data->getAccountId());

        if (!$affiliateAccount->getId()) {
            throw new LocalizedException(__('Affiliate doesn\'t exist!'));
        }

        /** @var \Magenest\Affiliate\Model\Transaction $transaction */
        $transaction = $this->transactionFactory->create();
        $data = new DataObject([
            'account_id' => $data->getAccountId(),
            'amount' => $data->getAmount(),
            'title' => $data->getTitle(),
            'hold_day' => $data->getHoldingTo()
        ]);
        try {
            $transaction->createTransaction('admin', $affiliateAccount, $data);
        } catch (Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }

        return $transaction->getId();
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return $this->searchResultFactory->create()->getTotalCount();
    }

    /**
     * {@inheritDoc}
     */
    public function cancel($id)
    {
        return $this->executeAction($id, true);
    }

    /**
     * {@inheritDoc}
     */
    public function complete($id)
    {
        return $this->executeAction($id);
    }

    /**
     * @param $id
     * @param $isCancel
     *
     * @return bool
     * @throws InputException
     * @throws LocalizedException
     */
    public function executeAction($id, $isCancel = false)
    {
        if (!$id) {
            throw new InputException(__('Id required'));
        }

        try {
            /** @var \Magenest\Affiliate\Model\Transaction $transaction */
            $transaction = $this->transactionFactory->create()->load($id);
            if ($isCancel) {
                $transaction->cancel();
            } else {
                $transaction->complete();
            }
            $result = true;
        } catch (Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function createTransactionByAction(TransactionInterface $data, $action)
    {
        $affiliateAccount = $this->_accountFactory->create()->load($data->getAccountId());

        if (!$affiliateAccount->getId()) {
            throw new LocalizedException(__('Affiliate doesn\'t exist!'));
        }

        /** @var \Magenest\Affiliate\Model\Transaction $transaction */
        $transaction = $this->transactionFactory->create();
        $data = new DataObject([
            'account_id' => $data->getAccountId(),
            'amount' => $data->getAmount(),
            'title' => $data->getTitle(),
        ]);
        try {
            $transaction->createTransaction($action, $affiliateAccount, $data);
        } catch (Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }

        return $transaction->getId();
    }
}
