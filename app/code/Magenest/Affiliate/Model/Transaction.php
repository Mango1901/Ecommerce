<?php


namespace Magenest\Affiliate\Model;

use Exception;
use Magenest\Affiliate\Observer\CreateAffiliateTransactionFromStoreCredit;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magenest\Affiliate\Helper\Data;
use Magenest\Affiliate\Model\Transaction\AbstractAction;
use Magenest\Affiliate\Model\Transaction\Status;
use Magenest\Affiliate\Model\Transaction\Type;

/**
 * Class Transaction
 * @package Magenest\Affiliate\Model
 * @method getAmount()
 * @method getTotalAmountHold()
 * @method setIsUpdateHoldingTransaction(bool $true)
 * @method getType()
 * @method getStatus()
 * @method getCustomerId()
 * @method getAccountId()
 * @method setStatus(int $STATUS_COMPLETED)
 * @method setAmount(float $param)
 * @method getAmountUsed()
 * @method getOrderId()
 */
class Transaction extends AbstractModel implements ExtensibleDataInterface
{
    const XML_PATH_EMAIL_ENABLE = 'email/transaction/enable';
    const XML_PATH_TRANSACTION_EMAIL_UPDATE_BALANCE_TEMPLATE = 'affiliate/email/transaction/update_balance';
    /**
     * Config action name
     *
     * @var string
     */
    const XML_CONFIG_ACTIONS = 'transaction';
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'affiliate_transaction';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'affiliate_transaction';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'affiliate_transaction';

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * Transaction constructor.
     *
     * @param Context $context
     * @param ManagerInterface $messageManager
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param ObjectManagerInterface $objectmanager
     * @param JsonHelper $json
     * @param Data $helper
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        ManagerInterface $messageManager,
        Registry $registry,
        StoreManagerInterface $storeManager,
        ObjectManagerInterface $objectmanager,
        JsonHelper $json,
        Data $helper,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->messageManager = $messageManager;
        $this->jsonHelper = $json;
        $this->helper = $helper;
        $this->objectManager = $objectmanager;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\Affiliate\Model\ResourceModel\Transaction');
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Prepare transaction data
     *
     * @param $actionCode
     * @param $account
     * @param $object
     * @param $extra
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function prepareData($actionCode, $account, $object, $extra)
    {
        $actionData = $this->getActionModel($actionCode)
            ->setData([
                'account' => $account,
                'object' => $object,
                'code' => $actionCode,
                'extra_content' => $extra
            ])->prepareTransaction();

        $this->setData($actionData);

        return $this;
    }

    /**
     * Create Transaction
     *
     * @param $actionCode
     * @param $account
     * @param $object
     * @param array $extra
     *
     * @return $this
     * @throws LocalizedException
     */
    public function createTransaction($actionCode, $account, $object, $extra = [])
    {
        if (!$account->getId()) {
            throw new LocalizedException(__('Affiliate account must be existed.'));
        }

        $this->prepareData($actionCode, $account, $object, $extra);

        /** Don't create transaction without amount */
        if (!$this->getAmount()) {
            throw new LocalizedException(__('Transaction amount cannot be zero.'));
        }

        /** If account is not enough and negative balance is not allow, Don't create transaction */
        if ($this->getAmount() < 0 && ($account->getBalance() + $this->getTotalAmountHold() + $this->getAmount() < 0)) {
            if (!$this->getConfig('account/balance/negative')) {
                throw new LocalizedException(
                    __('Account balance is not enough to create this transaction.')
                );
            }
        }

        $dbTransaction = $this->objectManager->create('\Magento\Framework\DB\Transaction');

        if ($this->getData('status') == Status::STATUS_HOLD) {
            $account->setHoldingBalance($account->getHoldingBalance() + $this->getAmount());
        } else {
            /** status Complete */
            if ($this->getAmount() > 0) {
                $balanceLimit = (double)$this->getConfig('account/balance/limit');

                if ($balanceLimit > 0
                    && ($account->getBalance() + $account->getHoldingBalance() + $this->getAmount()) > $balanceLimit) {
                    if ($balanceLimit > ($account->getBalance() + $account->getHoldingBalance())) {
                        $this->setAmount($balanceLimit - ($account->getBalance() + $account->getHoldingBalance()));
                        $account->setBalance($balanceLimit);
                    } else {
                        throw new LocalizedException(__('Account balance has been reached the limit. Please contact to store owner.'));
                    }
                } else {
                    $account->setBalance($account->getBalance() + $this->getAmount());
                }
            } else {
                if ($this->getTotalAmountHold()) {
                    if (abs($this->getAmount()) > $this->getTotalAmountHold()) {
                        $account->setHoldingBalance($account->getHoldingBalance() - $this->getTotalAmountHold());
                        $account->setBalance($account->getBalance() + $this->getAmount() + $this->getTotalAmountHold());
                    } else {
                        $account->setHoldingBalance($account->getHoldingBalance() + $this->getAmount());
                    }

                    $this->setIsUpdateHoldingTransaction(true);
                } else {
                    $account->setBalance($account->getBalance() + $this->getAmount());
                }
            }

            $dbTransaction->addCommitCallback([$this, 'sendUpdateBalanceEmail']);
        }

        if ($this->getType() == Type::COMMISSION) {
            $account->setTotalCommission($account->getTotalCommission() + $this->getAmount());
        } elseif ($this->getType() == Type::PAID) {
            $account->setTotalPaid($account->getTotalPaid() + abs($this->getAmount()));
        }

        $dbTransaction->addObject($account)
            ->addObject($this)
            ->addCommitCallback([$this, 'updateAmountUsed'])
            ->save();

        // Ignore event dispatch from affiliate transaction to Avoid cyclical event loops with next round in store credit transaction
        if (!$this->_registry->registry(CreateAffiliateTransactionFromStoreCredit::IGNORE_EVENT_TRANSACTION_AFFILIATE)) {
            $this->_eventManager->dispatch($this->_eventPrefix . '_created', $this->_getEventData());
            $this->_eventManager->dispatch($this->_eventPrefix . '_created_' . $actionCode, $this->_getEventData());
        } else {
            $this->_registry->unregister(CreateAffiliateTransactionFromStoreCredit::IGNORE_EVENT_TRANSACTION_AFFILIATE);
        }

        return $this;
    }

    /**
     * Cancel transaction
     *
     * @return $this
     * @throws Exception
     */
    public function cancel()
    {
        if (!$this->getId() || $this->getStatus() == Status::STATUS_CANCELED) {
            throw new Exception(
                __('Invalid transaction data for canceling.')
            );
        }

        $dbTransaction = $this->objectManager->create('\Magento\Framework\DB\Transaction');
        $account = $this->getAffiliateAccount();
        $cancelAmount = $this->getAmount() - $this->getAmountUsed();
        if ($this->getStatus() == Status::STATUS_HOLD) {
            $holdingBalance = $account->getHoldingBalance() - $cancelAmount;
            if ($account->getHoldingBalance() - $cancelAmount < 0) {
                $holdingBalance = 0;
            }
            $account->setHoldingBalance($holdingBalance);
        } elseif ($this->getStatus() == Status::STATUS_COMPLETED) {
            $account->setBalance($account->getBalance() - $cancelAmount);
            if ($account->getBalance() < 0 && !$this->getConfig('account/balance/negative')) {
                throw new Exception(
                    __('Account balance is not enough for canceling.')
                );
            }
            $account->setTotalPaid($account->getTotalPaid() + $cancelAmount);
            if ($account->getTotalPaid() < 0) {
                throw new Exception(
                    __('Total paid cannot be negative.')
                );
            }
            $dbTransaction->addCommitCallback([$this, 'sendUpdateBalanceEmail']);
        }
        $this->setStatus(Status::STATUS_CANCELED);

        $dbTransaction->addObject($account)
            ->addObject($this)
            ->save();

        return $this;
    }

    /**
     * Complete transaction
     *
     * @return mixed
     * @throws Exception
     */
    public function complete()
    {
        if (!$this->getId() || $this->getAmount() <= 0 || $this->getStatus() >= Status::STATUS_COMPLETED
        ) {
            return false;
        }

        $dbTransaction = $this->objectManager->create('\Magento\Framework\DB\Transaction');
        $account = $this->getAffiliateAccount();
        $completeAmount = $this->getAmount() - $this->getAmountUsed();
        if ($this->getStatus() == Status::STATUS_HOLD) {
            $order = $this->objectManager->create('Magento\Sales\Model\Order')->load($this->getOrderId());
            if ($order->getId() && $order->getAffiliateCommissionHoldingRefunded()) {
                $orderCommissionHolding = $this->helper->unserialize($order->getAffiliateCommissionHoldingRefunded());
                $accountId = $account->getAccountId();
                if (is_array($orderCommissionHolding) && isset($orderCommissionHolding[$accountId])) {
                    $orderCommissionHoldingById = $orderCommissionHolding[$accountId];
                    if ($completeAmount >= $orderCommissionHoldingById) {
                        $completeAmount = $completeAmount - $orderCommissionHoldingById;
                        $orderCommissionHolding[$accountId] = 0;
                    } else {
                        $completeAmount = 0;
                        $orderCommissionHolding[$accountId] = $orderCommissionHoldingById - $completeAmount;
                    }
                    $order->setAffiliateCommissionHoldingRefunded($this->helper->serialize($orderCommissionHolding));
                    $dbTransaction->addObject($order);
                }
            }

            $holdingBalance = $account->getHoldingBalance() - $completeAmount;
            $account->setHoldingBalance($holdingBalance < 0 ? 0 : $holdingBalance);
        }
        $balanceLimit = (double)$this->getConfig('account/balance/limit');
        if ($balanceLimit > 0 && $this->getAmount() > 0 && ($account->getBalance() + $completeAmount) > $balanceLimit) {
            if ($balanceLimit > $account->getBalance()) {
                $account->setBalance($balanceLimit);
            } else {
                throw new Exception(
                    __('Maximum amount allowed in account balance is %1.', $balanceLimit)
                );
            }
        } else {
            $account->setBalance($account->getBalance() + $completeAmount);
        }
        $this->setStatus(Status::STATUS_COMPLETED);

        $dbTransaction->addObject($account)
            ->addObject($this)
            ->addCommitCallback([$this, 'sendUpdateBalanceEmail'])
            ->save();

        return $this;
    }

    /**
     * Get action model for transaction action
     *
     * @param $code
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function getActionModel($code)
    {
        $actionClass = $this->getConfig(self::XML_CONFIG_ACTIONS . '/' . $code);
        if ($actionClass) {
            $action = $this->objectManager->create($actionClass);
            if (is_object($action) && ($action instanceof AbstractAction)) {
                return $action;
            }
        }

        throw new LocalizedException(__('Action model is invalid for %1', $code));
    }

    /**
     * Get Config value
     *
     * @param $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getConfig($code, $storeId = null)
    {
        return $this->helper->getModuleConfig($code, $storeId);
    }

    /**
     * Load Affiliate Account
     *
     * @return mixed
     */
    public function getAffiliateAccount()
    {
        if (!$this->hasData('affiliate_account')) {
            $this->setData(
                'affiliate_account',
                $this->objectManager->create('\Magenest\Affiliate\Model\Account')->load($this->getAccountId())
            );
        }

        return $this->getData('affiliate_account');
    }

    /**
     * Load Customer
     *
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->objectManager->create('\Magento\Customer\Model\Customer')->load($this->getCustomerId());
    }

    /**
     * Pricing Helper
     *
     * @return mixed
     */
    public function getPricingHelper()
    {
        return $this->objectManager->create('\Magento\Framework\Pricing\Helper\Data');
    }

    /**
     * Send email update balance
     *
     * @return $this
     */
    public function sendUpdateBalanceEmail()
    {
        $account = $this->getAffiliateAccount();
        if (!$this->helper->allowSendEmail($account, self::XML_PATH_EMAIL_ENABLE)) {
            return $this;
        }

        $customer = $this->getCustomer();
        if (!$customer || !$customer->getId()) {
            return $this;
        }

        try {
            $this->helper->sendEmailTemplate(
                $customer,
                self::XML_PATH_TRANSACTION_EMAIL_UPDATE_BALANCE_TEMPLATE,
                ['account' => $account, 'transaction' => $this]
            );
        } catch (LocalizedException $e) {
            $this->_logger->debug($e->getMessage().".\x20Please check the email server and retry.");
            return $this;
        }

        return $this;
    }

    /**
     * @param $store
     *
     * @return mixed
     */
    public function getAmountFormated($store)
    {
        return $this->getPricingHelper()->currencyByStore($this->getAmount(), $store->getId(), true, false);
    }

    /**
     * @return mixed
     */
    public function getStatusLabel()
    {
        $statusModel = $this->objectManager->create('\Magenest\Affiliate\Model\Transaction\Status');
        $statusHash = $statusModel->getOptionHash();

        return $statusHash[$this->getStatus()];
    }

    /**
     * Todo: Update amount used for transaction
     *
     * @return $this
     * @throws LocalizedException
     */
    public function updateAmountUsed()
    {
        $resource = $this->_getResource();
        if ($resource && $this->getAmount() < 0) {
            $resource->updateAmountUsed($this);
        }

        return $this;
    }
}
