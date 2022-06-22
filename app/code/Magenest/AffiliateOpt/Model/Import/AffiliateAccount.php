<?php

namespace Magenest\AffiliateOpt\Model\Import;

use Exception;
use Magento\Backend\Model\Auth;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\StringUtils;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ImportFactory;
use Magento\ImportExport\Model\ResourceModel\Helper;
use Magento\Store\Model\System\Store;
use Magenest\Affiliate\Model\Account\Group;
use Magenest\Affiliate\Model\Account\Status;
use Magenest\Affiliate\Model\AccountFactory;

/**
 * Class AffiliateAccount
 *
 * @package Magenest\AffiliateOpt\Model\Import
 */
class AffiliateAccount extends AbstractEntity
{
    /**
     * columns
     */
    const CUSTOMER_EMAIL = 'customer_email';
    const GROUP = 'group_id';
    const BALANCE = 'balance';
    const HOLDING_BALANCE = 'holding_balance';
    const TOTAL_COMMISSION = 'total_commission';
    const STATUS = 'status';
    const PARENT = 'parent';
    const STORE_ID = 'store_id';
    const WEBSITE_ID = 'website_id';

    /** @inheritdoc */
    protected $masterAttributeCode = 'customer_email';

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
     * Permanent entity columns.
     *
     * @var string[]
     */
    protected $_permanentAttributes
        = [
            self::CUSTOMER_EMAIL,
            self::GROUP,
            self::BALANCE,
            self::STATUS,
            self::STORE_ID,
            self::WEBSITE_ID
        ];

    /** @inheritdoc */
    protected $_availableBehaviors
        = [
            Import::BEHAVIOR_APPEND,
            Import::BEHAVIOR_DELETE
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
            self::CUSTOMER_EMAIL,
            self::GROUP,
            self::BALANCE,
            self::HOLDING_BALANCE,
            self::TOTAL_COMMISSION,
            self::STATUS,
            self::PARENT,
            self::STORE_ID,
            self::WEBSITE_ID
        ];

    /**
     * @var Group
     */
    protected $group;

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
     * @var AccountManagementInterface
     */
    protected $customerManagement;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var CustomerInterfaceFactory
     */
    protected $customerDataFactory;

    /**
     * @var GroupManagementInterface
     */
    protected $groupManagementInterface;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    /**
     * @var AccountFactory
     */
    protected $accountFactory;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var array
     */
    protected $affiliateGroups = [];

    /**
     * @var array
     */
    protected $stores = [];

    /**
     * @var array
     */
    protected $websites = [];

    /**
     * AffiliateAccount constructor.
     *
     * @param StringUtils                        $string
     * @param ScopeConfigInterface               $scopeConfig
     * @param ImportFactory                      $importFactory
     * @param Helper                             $resourceHelper
     * @param ResourceConnection                 $resource
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param Group                              $group
     * @param Status                             $status
     * @param Store                              $store
     * @param AccountManagementInterface         $accountManagement
     * @param CustomerInterfaceFactory           $customerDataFactory
     * @param DataObjectHelper                   $dataObjectHelper
     * @param GroupManagementInterface           $groupManagementInterface
     * @param CustomerRepositoryInterface        $customerRepositoryInterface
     * @param AccountFactory                     $accountFactory
     * @param CustomerFactory                    $customerFactory
     * @param array                              $data
     */
    public function __construct(
        StringUtils $string,
        ScopeConfigInterface $scopeConfig,
        ImportFactory $importFactory,
        Helper $resourceHelper,
        ResourceConnection $resource,
        ProcessingErrorAggregatorInterface $errorAggregator,
        Group $group,
        Status $status,
        Store $store,
        AccountManagementInterface $accountManagement,
        CustomerInterfaceFactory $customerDataFactory,
        DataObjectHelper $dataObjectHelper,
        GroupManagementInterface $groupManagementInterface,
        CustomerRepositoryInterface $customerRepositoryInterface,
        AccountFactory $accountFactory,
        CustomerFactory $customerFactory,
        array $data = []
    ) {
        $this->group                       = $group;
        $this->status                      = $status;
        $this->store                       = $store;
        $this->customerManagement          = $accountManagement;
        $this->customerDataFactory         = $customerDataFactory;
        $this->dataObjectHelper            = $dataObjectHelper;
        $this->groupManagementInterface    = $groupManagementInterface;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->accountFactory              = $accountFactory;
        $this->customerFactory             = $customerFactory;

        parent::__construct($string, $scopeConfig, $importFactory, $resourceHelper, $resource, $errorAggregator, $data);
    }

    /**
     * Entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'mp_affiliate_account';
    }

    /**
     * @return array
     */
    public function getAffiliateGroups()
    {
        if (!$this->affiliateGroups) {
            $groups = [];
            foreach ($this->group->getGroupCollection() as $group) {
                $groups[] = $group->getId();
            }
            $this->affiliateGroups = $groups;
        }

        return $this->affiliateGroups;
    }

    /***
     * @return array
     */
    public function getStores()
    {
        if (!$this->stores) {
            $this->stores = $this->store->getStoreOptionHash();
        }

        return $this->stores;
    }

    /***
     * @return array
     */
    public function getWebsites()
    {
        if (!$this->websites) {
            $this->websites = $this->store->getWebsiteOptionHash();
        }

        return $this->websites;
    }

    /**
     * @param $rowData
     * @param $rowNum
     *
     * @return bool
     */
    public function validateEmail($rowData, $rowNum)
    {
        if (!filter_var($rowData[self::CUSTOMER_EMAIL], FILTER_VALIDATE_EMAIL)) {
            $this->addRowError(__('Invalid email'), $rowNum);

            return false;
        }

        return true;
    }

    /**
     * Row validation.
     *
     * @param array $rowData
     * @param int   $rowNum
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
        if (Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            if (!isset($rowData[self::CUSTOMER_EMAIL])) {
                $this->addRowError(__('customer_email is empty'), $rowNum);
                $flag = false;
            } else {
                $flag = $this->validateEmail($rowData, $rowNum);
            }

            return $flag;
        }

        $groups   = $this->getAffiliateGroups();
        $status   = $this->status->toOptionHash();
        $stores   = $this->getStores();
        $websites = $this->getWebsites();
        foreach ($this->_permanentAttributes as $value) {
            if (empty($rowData[$value])) {
                $this->addRowError(__('%1 is empty', $value), $rowNum);
                $flag = false;
            } else {
                if ($value == self::CUSTOMER_EMAIL) {
                    $flag = $this->validateEmail($rowData, $rowNum);
                }

                if ($value == self::GROUP && !in_array($rowData[self::GROUP], $groups)) {
                    $this->addRowError(__('Affiliate group doesn\'t exist'), $rowNum);
                    $flag = false;
                }

                if ($value == self::BALANCE && $rowData[self::BALANCE] < 0) {
                    $this->addRowError(__('Balance must equals or greater than zero'), $rowNum);
                    $flag = false;
                }

                if ($value == self::STATUS && !isset($status[$rowData[self::STATUS]])) {
                    $this->addRowError(__('Affiliate status doesn\'t exist'), $rowNum);
                    $flag = false;
                }

                if ($value == self::STORE_ID && !isset($stores[$rowData[self::STORE_ID]])) {
                    $this->addRowError(__('store_id doesn\'t exist'), $rowNum);
                    $flag = false;
                }
                if ($value == self::WEBSITE_ID && !isset($websites[$rowData[self::WEBSITE_ID]])) {
                    $this->addRowError(__('website_id doesn\'t exist'), $rowNum);
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
        if ($this->getBehavior() == Import::BEHAVIOR_DELETE) {
            $this->deleteEntity();
        } else {
            $this->saveEntity();
        }

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
                    $entityList[$rowNum][$field] = $rowData[$field];
                }

                if (isset($rowData[self::HOLDING_BALANCE])) {
                    $entityList[$rowNum][self::HOLDING_BALANCE] = $rowData[self::HOLDING_BALANCE];
                }

                if (isset($rowData[self::TOTAL_COMMISSION])) {
                    $entityList[$rowNum][self::TOTAL_COMMISSION] = $rowData[self::TOTAL_COMMISSION];
                }

                if (isset($rowData[self::PARENT])) {
                    $entityList[$rowNum][self::PARENT] = $rowData[self::PARENT];
                }
            }
        }

        $this->saveEntityFinish($entityList);

        return $this;
    }

    /**
     * @param $data
     *
     * @return CustomerInterface
     * @throws LocalizedException
     */
    public function createCustomer($data)
    {
        $customer = $this->customerDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $customer,
            [
                'email'              => $data['customer_email'],
                'website_id'         => $data['website_id'],
                'firstname'          => 'firstname',
                'lastname'           => 'lastname',
                'group'              => $this->groupManagementInterface->getDefaultGroup($data['store_id']),
                'sendemail_store_id' => $data['store_id']
            ],
            CustomerInterface::class
        );

        return $this->customerManagement->createAccount($customer);
    }

    /**
     * @param array $entityData
     *
     * @return $this
     * @throws Exception
     */
    public function saveEntityFinish(array $entityData)
    {
        $connection = $this->_connection;
        $connection->beginTransaction();
        try {
            foreach ($entityData as $key => $data) {
                $customer = $this->customerFactory->create();
                $customer->setWebsiteId($data['website_id']);
                $customer->loadByEmail($data['customer_email']);
                if (!$customer->getEntityId()) {
                    $customer = $this->createCustomer($data);
                }

                $data['customer_id'] = $customer->getId();
                $account             = $this->accountFactory->create();
                if ($account->load($customer->getId(), 'customer_id')->getId()) {
                    $account->setBalance($account->getBalance() + abs($data[self::BALANCE]));
                    if (isset($data[self::HOLDING_BALANCE])) {
                        $account->setHoldingBalance($account->getHoldingBalance() + abs($data[self::HOLDING_BALANCE]));
                    }

                    if (isset($data[self::TOTAL_COMMISSION])) {
                        $account->setTotalCommission($account->getTotalCommission()+ abs($data[self::TOTAL_COMMISSION]));
                    }
                    $this->countItemsUpdated += 1;
                } else {
                    $account->setData($data);
                    $this->countItemsCreated += 1;
                }
                $account->save();
            }

            $connection->commit();
        } catch (Exception $e) {
            $this->addException($e);

            $connection->rollBack();
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function deleteEntity()
    {
        $listEmails = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
                $this->validateRow($rowData, $rowNum);
                if (!$this->getErrorAggregator()->isRowInvalid($rowNum)) {
                    $email        = $rowData[self::CUSTOMER_EMAIL];
                    $listEmails[] = $email;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                }
            }
        }

        $accountCollection = $this->accountFactory->create()->getCollection();
        $accountCollection->getSelect()->join(
            ['customer' => $accountCollection->getTable('customer_entity')],
            'main_table.customer_id = customer.entity_id',
            ['email']
        );
        $accountCollection->addFilterToMap('email', 'customer.email');
        $accountCollection->addFieldToFilter('email', ['in' => $listEmails]);

        try {
            foreach ($accountCollection as $account) {
                $account->delete();
                $this->countItemsDeleted += 1;
            }
        } catch (Exception $e) {
            $this->addException($e);
        }

        return $this;
    }

    /**
     * @param $e
     *
     * @return $this
     */
    public function addException($e)
    {
        $errorAggregator = $this->getErrorAggregator();
        $errorAggregator->addError(
            AbstractEntity::ERROR_CODE_SYSTEM_EXCEPTION,
            ProcessingError::ERROR_LEVEL_CRITICAL,
            null,
            null,
            $e->getMessage()
        );

        return $this;
    }
}
