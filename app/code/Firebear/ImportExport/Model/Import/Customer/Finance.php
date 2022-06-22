<?php
/**
 * @copyright: Copyright © 2018 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model\Import\Customer;

use Firebear\ImportExport\Traits\Import\Entity as ImportTrait;
use Magento\CustomerBalance\Model\BalanceFactory;
use Magento\CustomerFinance\Helper\Data as HelperData;
use Magento\CustomerFinance\Model\ResourceModel\Customer\Attribute\Finance\Collection as FinanceCollection;
use Magento\ImportExport\Model\Import\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\Reward\Model\RewardFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class Finance
 *
 * @package Firebear\ImportExport\Model\Import\Customer
 */
class Finance extends \Magento\CustomerImportExport\Model\Import\AbstractCustomer
{
    use ImportTrait;

    /**
     * Attribute collection name
     */
    const ATTRIBUTE_COLLECTION_NAME = FinanceCollection::class;

    /**#@+
     * Permanent column names
     *
     * Names that begins with underscore is not an attribute. This name convention is for
     * to avoid interference with same attribute name.
     */
    const COLUMN_EMAIL = '_email';

    const COLUMN_FINANCE_WEBSITE = '_finance_website';

    const COLUMN_WEBSITE = '_website';

    /**#@+
     * Error codes
     */
    const ERROR_INVALID_FINANCE_WEBSITE = 'invalidFinanceWebsite';

    const ERROR_DUPLICATE_PK = 'duplicateEmailSiteFinanceSite';

    const ERROR_FINANCE_WEBSITE_IS_EMPTY = 'financeWebsiteIsEmpty';

    /**#@-*/
    protected $_permanentAttributes = [self::COLUMN_WEBSITE, self::COLUMN_EMAIL, self::COLUMN_FINANCE_WEBSITE];

    /**
     * Valid column names
     *
     * @array
     */
    protected $validColumnNames = [
        FinanceCollection::COLUMN_CUSTOMER_BALANCE,
        FinanceCollection::COLUMN_REWARD_POINTS,
    ];

    /**
     * Column names that holds values with particular meaning
     *
     * @var string[]
     */
    protected $_specialAttributes = [
        self::COLUMN_ACTION,
        self::COLUMN_WEBSITE,
        self::COLUMN_EMAIL,
        self::COLUMN_FINANCE_WEBSITE,
    ];

    /**
     * Comment for finance data import
     *
     * @var string
     */
    protected $_comment;

    /**
     * Helper to check whether modules are enabled/disabled
     *
     * @var HelperData
     */
    protected $_customerFinanceData;

    /**
     * Address attributes collection
     *
     * @var \Magento\CustomerFinance\Model\ResourceModel\Customer\Attribute\Finance\Collection
     */
    protected $_attributeCollection;

    /**
     * Admin user object
     *
     * @var \Magento\User\Model\User
     */
    protected $_adminUser;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * Store imported row primary keys
     *
     * @var array
     */
    protected $_importedRowPks = [];

    /**
     * @var RewardFactory
     */
    protected $_rewardFactory;

    /**
     * @var BalanceFactory
     */
    protected $_balanceFactory;

    protected $_debugMode;

    protected $duplicateFields = [];

    /**
     * @var \Firebear\ImportExport\Model\IntegrationFactory
     */
    protected $_integrationFactory;

    /**
     * Finance constructor.
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\ImportExport\Model\ImportFactory $importFactory
     * @param \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\ImportExport\Model\Export\Factory $collectionFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\CustomerImportExport\Model\ResourceModel\Import\Customer\StorageFactory $storageFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param ConsoleOutput $output
     * @param \Firebear\ImportExport\Helper\Data $helper
     * @param LoggerInterface $logger
     * @param \Firebear\ImportExport\Model\ResourceModel\Import\Data $importFireData
     * @param \Firebear\ImportExport\Model\IntegrationFactory $integrationFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\ImportExport\Model\ImportFactory $importFactory,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\App\ResourceConnection $resource,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\ImportExport\Model\Export\Factory $collectionFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\CustomerImportExport\Model\ResourceModel\Import\Customer\StorageFactory $storageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Symfony\Component\Console\Output\ConsoleOutput $output,
        \Firebear\ImportExport\Helper\Data $helper,
        LoggerInterface $logger,
        \Firebear\ImportExport\Model\ResourceModel\Import\Data $importFireData,
        \Firebear\ImportExport\Model\IntegrationFactory $integrationFactory,
        array $data = []
    ) {
        $data['entity_type_id'] = -1;

        parent::__construct(
            $string,
            $scopeConfig,
            $importFactory,
            $resourceHelper,
            $resource,
            $errorAggregator,
            $storeManager,
            $collectionFactory,
            $eavConfig,
            $storageFactory,
            $data
        );

        $this->_customerFactory = $customerFactory;
        $this->_logger = $logger;
        $this->output = $output;
        $this->_debugMode = $helper->getDebugMode();
        $this->_dataSourceModel = $importFireData;
        $this->_integrationFactory = $integrationFactory;

        $this->_adminUser = isset($data['admin_user']) ? $data['admin_user'] : $authSession->getUser();

        $this->addMessageTemplate(
            self::ERROR_FINANCE_WEBSITE_IS_EMPTY,
            __('Please specify a finance information website.')
        );
        $this->addMessageTemplate(
            self::ERROR_INVALID_FINANCE_WEBSITE,
            __('Please specify a valid finance information website.')
        );
        $this->addMessageTemplate(
            self::ERROR_DUPLICATE_PK,
            __('A row with this email, website, and finance website combination already exists.')
        );
        $this->_initAttributes();
    }

    /**
     * Initialize entity attributes
     *
     * @return $this
     */
    protected function _initAttributes()
    {
        /** @var $attribute \Magento\Eav\Model\Attribute */
        foreach ($this->_attributeCollection as $attribute) {
            $this->_attributes[$attribute->getAttributeCode()] = [
                'id' => $attribute->getId(),
                'type' => $attribute->getBackendType(),
                'is_required' => $attribute->getIsRequired(),
                'code' => $attribute->getAttributeCode(),
            ];
        }
        return $this;
    }

    /**
     * Update reward points value for customerEtn
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @param int $websiteId
     * @param int $value reward points value
     * @return \Magento\Reward\Model\Reward
     */
    protected function _updateRewardPointsForCustomer(\Magento\Customer\Model\Customer $customer, $websiteId, $value)
    {
        /** @var $reward \Magento\Reward\Model\Reward */
        if (empty($this->_rewardFactory)) {
            $this->_rewardFactory = $this->_integrationFactory->create(RewardFactory::class);
        }
        $reward = $this->_rewardFactory->create();
        $reward->setCustomer($customer)->setWebsiteId($websiteId)->loadByCustomer();
        $reward = $this->_updateRewardValue($reward, $value);
        return $reward;
    }

    /**
     * Update reward points value for reward model
     *
     * @param \Magento\Reward\Model\Reward $rewardModel
     * @param int $value reward points value
     * @return \Magento\Reward\Model\Reward
     */
    protected function _updateRewardValue(\Magento\Reward\Model\Reward $reward, $value)
    {
        $delta = $value - $reward->getPointsBalance();
        if ($delta != 0) {
            $reward->setPointsDelta(
                $delta
            )->setAction(
                \Magento\Reward\Model\Reward::REWARD_ACTION_ADMIN
            )->setComment(
                $this->_getComment()
            )->updateRewardPoints();
        }

        return $reward;
    }

    /**
     * Update store credit balance for customer
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @param int $websiteId
     * @param float $value store credit balance
     * @return \Magento\CustomerBalance\Model\Balance
     */
    protected function _updateCustomerBalanceForCustomer(
        \Magento\Customer\Model\Customer $customer,
        $websiteId,
        $value
    ) {
        /** @var $balance \Magento\CustomerBalance\Model\Balance */
        if (empty($this->_balanceFactory)) {
            $this->_balanceFactory = $this->_integrationFactory->create(BalanceFactory::class);
        }
        $balance = $this->_balanceFactory->create();
        $balance->setCustomer($customer)->setWebsiteId($websiteId)->loadByCustomer();

        return $this->_updateCustomerBalanceValue($balance, $value);
    }

    /**
     * Update balance for customer balance model
     *
     * @param \Magento\CustomerBalance\Model\Balance $balanceModel
     * @param float $value store credit balance
     * @return \Magento\CustomerBalance\Model\Balance
     */
    protected function _updateCustomerBalanceValue(\Magento\CustomerBalance\Model\Balance $balance, $value)
    {
        $delta = $value - $balance->getAmount();
        if ($delta != 0) {
            $balance->setAmountDelta($delta)->setComment($this->_getComment())->save();
        }

        return $balance;
    }

    /**
     * Delete reward points value for customer (just set it to 0)
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @param int $websiteId
     * @return void
     */
    protected function _deleteRewardPoints(\Magento\Customer\Model\Customer $customer, $websiteId)
    {
        $this->_updateRewardPointsForCustomer($customer, $websiteId, 0);
    }

    /**
     * Imported entity type code getter
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'customer_finance';
    }

    /**
     * @return array
     */
    public function getAllFields()
    {
        $options = array_merge($this->getValidColumnNames(), $this->_specialAttributes);
        $options = array_merge($options, $this->_permanentAttributes);

        return array_unique($options);
    }

    /**
     * Delete store credit balance for customer (just set it to 0)
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @param int $websiteId
     * @return void
     */
    protected function _deleteCustomerBalance(\Magento\Customer\Model\Customer $customer, $websiteId)
    {
        $this->_updateCustomerBalanceForCustomer($customer, $websiteId, 0);
    }

    protected function _importData()
    {
        if (empty($this->_customerFinanceData)) {
            $this->_customerFinanceData = $this->_integrationFactory->create(HelperData::class);
        }
        $isRewardPointsEnabled = $this->_customerFinanceData->isRewardPointsEnabled();
        $isCustomerBalanceEnabled = $this->_customerFinanceData->isCustomerBalanceEnabled();
        if (!$isRewardPointsEnabled && !$isCustomerBalanceEnabled) {
            return false;
        }

        /** @var $customer \Magento\Customer\Model\Customer */
        $customer = $this->_customerFactory->create();
        $rewardPointsKey = FinanceCollection::COLUMN_REWARD_POINTS;
        $customerBalanceKey = FinanceCollection::COLUMN_CUSTOMER_BALANCE;

        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNumber => $rowData) {
                $time = explode(" ", microtime());
                $startTime = $time[0] + $time[1];
                $email = $rowData['_email'];
                // check row data
                if (!$this->validateRow($rowData, $rowNumber)) {
                    $this->addLogWriteln(__('email: %1 is not valided', $email), $this->output, 'info');

                    continue;
                }
                // load customer object
                $cId = $this->_getCustomerId($rowData[self::COLUMN_EMAIL], $rowData[self::COLUMN_WEBSITE]);
                if ($customer->getId() != $cId) {
                    $customer->reset();
                    $customer->load($cId);
                }

                $websiteId = $this->_websiteCodeToId[$rowData[self::COLUMN_FINANCE_WEBSITE]];
                // save finance data for customer
                foreach ($this->_attributes as $attrCode => $attrParams) {
                    if ($this->getBehavior($rowData) == \Magento\ImportExport\Model\Import::BEHAVIOR_DELETE) {
                        if ($attrCode == $rewardPointsKey) {
                            $this->_deleteRewardPoints($customer, $websiteId);
                        } elseif ($attrCode == $customerBalanceKey) {
                            $this->_deleteCustomerBalance($customer, $websiteId);
                        }
                    } elseif ($this->getBehavior($rowData) == \Magento\ImportExport\Model\Import::BEHAVIOR_ADD_UPDATE
                    ) {
                        if (isset($rowData[$attrCode]) && strlen($rowData[$attrCode])) {
                            if ($attrCode == $rewardPointsKey) {
                                $this->_updateRewardPointsForCustomer($customer, $websiteId, $rowData[$attrCode]);
                            } elseif ($attrCode == $customerBalanceKey) {
                                $this->_updateCustomerBalanceForCustomer(
                                    $customer,
                                    $websiteId,
                                    $rowData[$attrCode]
                                );
                            }
                        }
                    }
                }
                $endTime = $time[0] + $time[1];
                $totalTime = $endTime - $startTime;
                $totalTime = round($totalTime, 5);
                $this->addLogWriteln(__('email: %1 .... %2s', $email, $totalTime), $this->output, 'info');
            }
        }

        return true;
    }

    protected function _saveValidatedBunches()
    {
        $source = $this->getSource();
        $bunchRows = [];
        $startNewBunch = false;

        $source->rewind();
        $this->_dataSourceModel->cleanBunches();
        $masterAttributeCode = $this->getMasterAttributeCode();
        $file = null;
        $jobId = null;
        if (isset($this->_parameters['file'])) {
            $file = $this->_parameters['file'];
        }
        if (isset($this->_parameters['job_id'])) {
            $jobId = $this->_parameters['job_id'];
        }
        while ($source->valid() || count($bunchRows) || isset($entityGroup)) {
            if ($startNewBunch || !$source->valid()) {
                /* If the end approached add last validated entity group to the bunch */
                if (!$source->valid() && isset($entityGroup)) {
                    foreach ($entityGroup as $key => $value) {
                        $bunchRows[$key] = $value;
                    }
                    unset($entityGroup);
                }
                $this->_dataSourceModel->saveBunches(
                    $this->getEntityTypeCode(),
                    $this->getBehavior(),
                    $jobId,
                    $file,
                    $bunchRows
                );

                $bunchRows = [];
                $startNewBunch = false;
            }
            if ($source->valid()) {
                $valid = true;
                try {
                    $rowData = $source->current();
                    foreach ($rowData as $attrName => $element) {
                        if (!mb_check_encoding($element, 'UTF-8')) {
                            $valid = false;
                            $this->addRowError(
                                AbstractEntity::ERROR_CODE_ILLEGAL_CHARACTERS,
                                $this->_processedRowsCount,
                                $attrName
                            );
                        }
                    }
                } catch (\InvalidArgumentException $e) {
                    $valid = false;
                    $this->addRowError($e->getMessage(), $this->_processedRowsCount);
                }
                if (!$valid) {
                    $this->_processedRowsCount++;
                    $source->next();
                    continue;
                }
                $rowData = $this->customBunchesData($rowData);
                if (isset($rowData[$masterAttributeCode]) && trim($rowData[$masterAttributeCode])) {
                    /* Add entity group that passed validation to bunch */
                    if (isset($entityGroup)) {
                        foreach ($entityGroup as $key => $value) {
                            $bunchRows[$key] = $value;
                        }
                        $productDataSize = strlen(\Zend\Serializer\Serializer::serialize($bunchRows));

                        /* Check if the new bunch should be started */
                        $isBunchSizeExceeded = ($this->_bunchSize > 0 && count($bunchRows) >= $this->_bunchSize);
                        $startNewBunch = $productDataSize >= $this->_maxDataSize || $isBunchSizeExceeded;
                    }

                    /* And start a new one */
                    $entityGroup = [];
                }

                if (isset($entityGroup) && $this->validateRow($rowData, $source->key())) {
                    /* Add row to entity group */
                    $entityGroup[$source->key()] = $this->_prepareRowForDb($rowData);
                } elseif (isset($entityGroup)) {
                    /* In case validation of one line of the group fails kill the entire group */
                    unset($entityGroup);
                }

                $this->_processedRowsCount++;
                $source->next();
            }
        }
        return $this;
    }

    protected function _getComment()
    {
        if (!$this->_comment && $this->_adminUser != null) {
            $this->_comment = __('Data was imported by %1', $this->_adminUser->getUsername());
        }

        return $this->_comment;
    }

    /**
     * Validate data row for add/update behaviour
     *
     * @param array $rowData
     * @param int $rowNumber
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _validateRowForUpdate(array $rowData, $rowNumber)
    {
        if ($this->_checkUniqueKey($rowData, $rowNumber)) {
            if (empty($rowData[self::COLUMN_FINANCE_WEBSITE])) {
                $this->addRowError(self::ERROR_FINANCE_WEBSITE_IS_EMPTY, $rowNumber, self::COLUMN_FINANCE_WEBSITE);
            } else {
                $financeWebsite = $rowData[self::COLUMN_FINANCE_WEBSITE];
                $website = $rowData[self::COLUMN_WEBSITE];
                $email = strtolower($rowData[self::COLUMN_EMAIL]);
                $customerId = $this->_getCustomerId($email, $website);

                $defaultStoreId = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
                if (!isset(
                    $this->_websiteCodeToId[$financeWebsite]
                ) || $this->_websiteCodeToId[$financeWebsite] == $defaultStoreId
                ) {
                    $this->addRowError(self::ERROR_INVALID_FINANCE_WEBSITE, $rowNumber, self::COLUMN_FINANCE_WEBSITE);
                } elseif ($customerId === false) {
                    $this->addRowError(self::ERROR_CUSTOMER_NOT_FOUND, $rowNumber);
                } elseif ($this->_checkRowDuplicate($customerId, $financeWebsite)) {
                    $this->addRowError(self::ERROR_DUPLICATE_PK, $rowNumber);
                } else {
                    foreach ($this->_attributes as $attributeCode => $attributeParams) {
                        if (in_array($attributeCode, $this->_ignoredAttributes)) {
                            continue;
                        }
                        if (isset($rowData[$attributeCode]) && strlen($rowData[$attributeCode])) {
                            $this->isAttributeValid($attributeCode, $attributeParams, $rowData, $rowNumber);
                        } elseif ($attributeParams['is_required']) {
                            $this->addRowError(self::ERROR_VALUE_IS_REQUIRED, $rowNumber, $attributeCode);
                        }
                    }
                }
            }
        }
    }

    /**
     * Validate data row for delete behaviour
     *
     * @param array $rowData
     * @param int $rowNumber
     * @return void
     */
    protected function _validateRowForDelete(array $rowData, $rowNumber)
    {
        if ($this->_checkUniqueKey($rowData, $rowNumber)) {
            if (empty($rowData[self::COLUMN_FINANCE_WEBSITE])) {
                $this->addRowError(self::ERROR_FINANCE_WEBSITE_IS_EMPTY, $rowNumber, self::COLUMN_FINANCE_WEBSITE);
            } else {
                $financeWebsite = $rowData[self::COLUMN_FINANCE_WEBSITE];
                $email = strtolower($rowData[self::COLUMN_EMAIL]);
                $website = $rowData[self::COLUMN_WEBSITE];
                $defaultStoreId = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
                if (!isset(
                    $this->_websiteCodeToId[$financeWebsite]
                ) || $this->_websiteCodeToId[$financeWebsite] == $defaultStoreId
                ) {
                    $this->addRowError(self::ERROR_INVALID_FINANCE_WEBSITE, $rowNumber, self::COLUMN_FINANCE_WEBSITE);
                } elseif (!$this->_getCustomerId($email, $website)) {
                    $this->addRowError(self::ERROR_CUSTOMER_NOT_FOUND, $rowNumber);
                }
            }
        }
    }

    /**
     * Check whether row with such email, website, finance website combination was already found in import file
     *
     * @param int $customerId
     * @param string $financeWebsite
     * @return bool
     */
    protected function _checkRowDuplicate($customerId, $financeWebsite)
    {
        $error = false;
        $financeWebsiteId = $this->_websiteCodeToId[$financeWebsite];
        if (!isset($this->_importedRowPks[$customerId][$financeWebsiteId])) {
            $this->_importedRowPks[$customerId][$financeWebsiteId] = true;
        } else {
            $error = true;
        }
        return $error;
    }
}
