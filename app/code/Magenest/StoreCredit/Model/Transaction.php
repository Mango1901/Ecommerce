<?php
/**
 * Magenest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magenest.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magenest.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magenest
 * @package     Magenest_StoreCredit
 * @copyright   Copyright (c) Magenest (https://www.magenest.com/)
 * @license     https://www.magenest.com/LICENSE.txt
 */

namespace Magenest\StoreCredit\Model;

use Exception;
use Magenest\StoreCredit\Observer\CreateStoreCreditTransactionFromAffiliate;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel as AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magenest\StoreCredit\Api\Data\TransactionInterface;
use Magenest\StoreCredit\Helper\Data as HelperData;
use Magenest\StoreCredit\Model\Action\ActionInterface;
use Magenest\StoreCredit\Model\Config\Source\Status;

/**
 * Class Transaction
 * @package Magenest\StoreCredit\Model
 */
class Transaction extends AbstractModel implements IdentityInterface, TransactionInterface
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'magenest_store_credit_transaction';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'magenest_store_credit_transaction';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'magenest_store_credit_transaction';

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var ActionInterface[]
     */
    protected $actionByCode = [];

    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * Transaction constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param HelperData $helperData
     * @param ActionFactory $actionFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        HelperData $helperData,
        ActionFactory $actionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->helperData = $helperData;
        $this->actionFactory = $actionFactory;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\StoreCredit\Model\ResourceModel\Transaction');
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @param string $code
     * @param \Magento\Customer\Model\Customer $customer
     * @param $actionObject
     *
     * @return $this
     * @throws LocalizedException
     */
    public function createTransaction($code, $customer, $actionObject)
    {
        $action = $this->getActionModel($code, ['customer' => $customer, 'actionObject' => $actionObject]);
        $transactionData = $action->prepareTransaction();

        $transactionData['action'] = $code;
        $this->setData($transactionData);

        $balance = $customer->getMpCreditBalance() + $this->getAmount();

        if ($balance < 0) {
            throw new LocalizedException(__('Customer balance is not sufficient to deduct credit.'));
        }

        $this->setBalance($balance);
        $customer->setMpCreditBalance($balance);

        try {
            $this->getResource()->saveTransaction($this, $customer);
        } catch (Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }

        $this->sendUpdateBalanceEmail();

        // Ignore event dispatch from store credit transaction to Avoid cyclical event loops with next round in affiliate transaction
        if (!$this->_registry->registry(CreateStoreCreditTransactionFromAffiliate::IGNORE_EVENT_TRANSACTION_STORE_CREDIT)) {
            $this->_eventManager->dispatch($this->_eventPrefix . '_created', $this->_getEventData());
            $this->_eventManager->dispatch($this->_eventPrefix . '_created_' . $code, $this->_getEventData());
        } else {
            $this->_registry->unregister(CreateStoreCreditTransactionFromAffiliate::IGNORE_EVENT_TRANSACTION_STORE_CREDIT);
        }

        return $this;
    }

    /**
     * @param $code
     * @param array $data
     *
     * @return ActionInterface
     */
    protected function getActionModel($code, $data = [])
    {
        if (!isset($this->actionByCode[$code])) {
            $this->actionByCode[$code] = $this->actionFactory->create($code, $data);
        }

        return $this->actionByCode[$code];
    }

    /**
     * @return string
     */
    public function getStatusLabel()
    {
        $statusArray = Status::getOptionArray();
        if (array_key_exists($this->getStatus(), $statusArray)) {
            return $statusArray[$this->getStatus()];
        }

        return '';
    }

    /**
     * @return $this
     */
    public function sendUpdateBalanceEmail()
    {
        $this->helperData->getEmailHelper()->sendEmailTemplate(
            $this->getCustomerId(),
            $this->getEmailParams()
        );

        return $this;
    }

    /**
     * @return array
     */
    public function getEmailParams()
    {
        $customer = $this->helperData->getAccountHelper()->getCustomerById($this->getCustomerId());

        $params = [
            'title' => $this->getTitle(),
            'amount' => $this->getAmount(),
            'balance' => $this->getBalance(),
            'status' => $this->getStatusLabel(),
            'formatted_amount' => $this->helperData->convertAndFormatPrice($this->getAmount(), false),
            'formatted_balance' => $this->helperData->convertAndFormatPrice($this->getBalance(), false),
            'current_balance' => $this->helperData->convertAndFormatPrice($customer->getMpCreditBalance(), false),
            'customer_note' => $this->getCustomerNote(),
            'customer_name' => $customer->getName(),
        ];

        return $params;
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb|ResourceModel\Transaction
     */
    public function getResource()
    {
        return parent::getResource();
    }

    /**
     * @param $customerId
     *
     * @return $this
     */
    public function loadByCustomerId($customerId)
    {
        return $this->load($customerId, 'customer_id');
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionId()
    {
        return $this->getData(self::TRANSACTION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setTransactionId($value)
    {
        return $this->setData(self::TRANSACTION_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId($value)
    {
        return $this->setData(self::CUSTOMER_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderId($value)
    {
        return $this->setData(self::ORDER_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($value)
    {
        return $this->setData(self::TITLE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($value)
    {
        return $this->setData(self::STATUS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAction()
    {
        return $this->getData(self::ACTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setAction($value)
    {
        return $this->setData(self::ACTION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAmount($value)
    {
        return $this->setData(self::AMOUNT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getBalance()
    {
        return $this->getData(self::BALANCE);
    }

    /**
     * {@inheritdoc}
     */
    public function setBalance($value)
    {
        return $this->setData(self::BALANCE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerNote()
    {
        return $this->getData(self::CUSTOMER_NOTE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerNote($value)
    {
        return $this->setData(self::CUSTOMER_NOTE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminNote()
    {
        return $this->getData(self::ADMIN_NOTE);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdminNote($value)
    {
        return $this->setData(self::ADMIN_NOTE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($value)
    {
        return $this->setData(self::CREATED_AT, $value);
    }
}
