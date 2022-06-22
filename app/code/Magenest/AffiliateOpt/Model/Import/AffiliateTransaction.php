<?php

namespace Magenest\AffiliateOpt\Model\Import;

use Exception;
use Magento\Backend\Model\Auth;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\StringUtils;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ImportFactory;
use Magento\ImportExport\Model\ResourceModel\Helper;
use Magenest\Affiliate\Model\AccountFactory;
use Magenest\Affiliate\Model\Transaction;
use Magenest\Affiliate\Model\Transaction\Status;
use Magenest\Affiliate\Model\Transaction\Type;
use Magenest\Affiliate\Model\TransactionFactory;
use Magenest\AffiliateOpt\Helper\Data;

/**
 * Class AffiliateTransaction
 * @package Magenest\AffiliateOpt\Model\Import
 */
class AffiliateTransaction extends AbstractEntity
{
    /**
     * columns
     */
    const ACCOUNT_EMAIL = 'account_email';
    const TITLE = 'title';
    const ACTION = 'action';
    const AMOUNT = 'amount';
    const STATUS = 'status';
    const ORDER_ID = 'order_id';
    const STORE_ID = 'store_id';
    const CREATED_AT = 'created_at';
    const EXTRA_CONTENT = 'extra_content';

    /** @inheritdoc */
    protected $masterAttributeCode = 'account_email';

    /**
     * Permanent entity columns.
     *
     * @var string[]
     */
    protected $_permanentAttributes = [self::ACCOUNT_EMAIL, self::ACTION, self::AMOUNT, self::STATUS];

    /** @inheritdoc */
    protected $_availableBehaviors = [
        Import::BEHAVIOR_ADD_UPDATE,
    ];

    /**
     * If we should check column names
     *
     * @var bool
     */
    protected $needColumnCheck = true;

    /**
     * Valid column names
     *
     * @array
     */
    protected $validColumnNames
        = [
            self::ACCOUNT_EMAIL,
            self::TITLE,
            self::ACTION,
            self::AMOUNT,
            self::STATUS,
            self::STATUS,
            self::ORDER_ID,
            self::STORE_ID,
            self::CREATED_AT,
            self::EXTRA_CONTENT
        ];

    protected $transactionFactory;

    /**
     * @var Status
     */
    protected $status;

    /**
     * @var Store
     */
    protected $store;

    /**
     * @var Auth
     */
    protected $_auth;

    /**
     * @var AccountFactory
     */
    protected $accountFactory;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var array
     */
    protected $accountEmails = [];

    /**
     * Count if created items
     *
     * @var int
     */
    protected $countItemsCreated = 0;

    /**
     * Count if updated items
     *
     * @var int
     */
    protected $countItemsUpdated = 0;

    /**
     * Count if deleted items
     *
     * @var int
     */
    protected $countItemsDeleted = 0;

    /**
     * AffiliateAccount constructor.
     *
     * @param StringUtils $string
     * @param ScopeConfigInterface $scopeConfig
     * @param ImportFactory $importFactory
     * @param Helper $resourceHelper
     * @param ResourceConnection $resource
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param Status $status
     * @param Auth $auth
     * @param AccountFactory $accountFactory
     * @param TransactionFactory $transactionFactory
     * @param DateTime $dateTime
     * @param array $data
     */
    public function __construct(
        StringUtils $string,
        ScopeConfigInterface $scopeConfig,
        ImportFactory $importFactory,
        Helper $resourceHelper,
        ResourceConnection $resource,
        ProcessingErrorAggregatorInterface $errorAggregator,
        Status $status,
        Auth $auth,
        AccountFactory $accountFactory,
        TransactionFactory $transactionFactory,
        DateTime $dateTime,
        array $data = []
    ) {
        $this->_auth = $auth;
        $this->status = $status;
        $this->accountFactory = $accountFactory;
        $this->transactionFactory = $transactionFactory;
        $this->dateTime = $dateTime;
        parent::__construct($string, $scopeConfig, $importFactory, $resourceHelper, $resource, $errorAggregator, $data);
    }

    /**
     * @return Transaction
     */
    public function getTransaction()
    {
        if (!$this->transaction) {
            $this->transaction = $this->transactionFactory->create();
        }

        return $this->transaction;
    }

    /**
     * Entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'mp_affiliate_transaction';
    }

    /**
     * @param $rowData
     * @param $rowNum
     *
     * @return bool
     */
    public function validateEmail($rowData, $rowNum)
    {
        $email = $rowData[self::ACCOUNT_EMAIL];
        if (!isset($this->accountEmails[$email])) {
            $accountCollection = $this->accountFactory->create()->getCollection();
            $accountCollection->getSelect()->join(
                ['customer' => $accountCollection->getTable('customer_entity')],
                'main_table.customer_id = customer.entity_id',
                ['email', 'entity_id']
            );
            $accountCollection->addFilterToMap('email', 'customer.email');
            $accountCollection->addFieldToFilter('email', $email);
            $account = $accountCollection->getFirstItem();
            if (!$account->getAccountId()) {
                $this->addRowError(__('Account email doesn\'t exist'), $rowNum);

                return false;
            }

            $this->accountEmails[$account->getEmail()] = $account;
        }

        return true;
    }

    /**
     * @param $rowData
     * @param $rowNum
     *
     * @return bool
     */
    public function validateAction($rowData, $rowNum)
    {
        try {
            $this->getTransaction()->getActionModel($rowData[self::ACTION]);
        } catch (Exception $e) {
            $this->addRowError($e->getMessage(), $rowNum);

            return false;
        }

        return true;
    }

    /**
     * Row validation.
     *
     * @param array $rowData
     * @param int $rowNum
     *
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum)
    {
        $flag = true;

        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }

        $this->_validatedRows[$rowNum] = true;
        $status = $this->status->getOptionHash();
        foreach ($this->_permanentAttributes as $value) {
            if (empty($rowData[$value])) {
                $this->addRowError(__('%1 is empty', $value), $rowNum);
                $flag = false;
            } else {
                if ($value == self::ACCOUNT_EMAIL) {
                    $flag = $this->validateEmail($rowData, $rowNum);
                }

                if ($value == self::AMOUNT && $rowData[self::AMOUNT] < 0) {
                    $this->addRowError(__('Amount must equals or greater than zero'), $rowNum);
                    $flag = false;
                }

                if ($value == self::ACTION) {
                    $flag = $this->validateAction($rowData, $rowNum);
                }

                if ($value == self::STATUS && !isset($status[$rowData[self::STATUS]])) {
                    $this->addRowError(__('Transaction status doesn\'t exist'), $rowNum);
                    $flag = false;
                }
            }
        }

        return $flag ? !$this->getErrorAggregator()->isRowInvalid($rowNum) : false;
    }

    /**
     * @return bool
     * @throws Exception
     */
    protected function _importData()
    {
        $this->saveEntity();

        return true;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function saveEntity()
    {
        $entityList = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->validateRow($rowData, $rowNum)) {
                    continue;
                }

                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }

                foreach ($this->_permanentAttributes as $field) {
                    if ($field == self::ACCOUNT_EMAIL) {
                        continue;
                    }
                    $entityList[$rowNum][$field] = $rowData[$field];
                }

                $currentAccount = $this->accountEmails[$rowData['account_email']];
                $entityList[$rowNum]['account_id'] = $currentAccount->getAccountId();
                $entityList[$rowNum]['customer_id'] = $currentAccount->getEntityId();
                $entityList[$rowNum]['type'] = Type::ADMIN;

                if (isset($rowData[self::TITLE])) {
                    $entityList[$rowNum][self::TITLE] = $rowData[self::TITLE];
                }

                if (isset($rowData[self::ORDER_ID])) {
                    $entityList[$rowNum][self::ORDER_ID] = $rowData[self::ORDER_ID];
                }

                if (isset($rowData[self::STORE_ID])) {
                    $entityList[$rowNum][self::STORE_ID] = $rowData[self::STORE_ID];
                }

                $date = $this->dateTime->gmtDate();
                if (isset($rowData[self::CREATED_AT]) && !empty($rowData[self::CREATED_AT])) {
                    $date = $this->dateTime->gmtDate(null, $rowData[self::CREATED_AT]);
                }
                $entityList[$rowNum][self::CREATED_AT] = $date;

                $entityList[$rowNum]['extra_content'] = Data::jsonEncode(['auth' => $this->_auth->getUser()->getName()]);
            }
        }
        $connection = $this->_connection;
        $connection->beginTransaction();
        try {
            $connection->insertMultiple(
                $this->transactionFactory->create()->getResource()->getMainTable(),
                $entityList
            );
            $connection->commit();
            $this->countItemsCreated += count($entityList);
        } catch (Exception $e) {
            $errorAggregator = $this->getErrorAggregator();
            $errorAggregator->addError(
                AbstractEntity::ERROR_CODE_SYSTEM_EXCEPTION,
                ProcessingError::ERROR_LEVEL_CRITICAL,
                null,
                null,
                $e->getMessage()
            );
            $connection->rollBack();
        }

        return $this;
    }
}
