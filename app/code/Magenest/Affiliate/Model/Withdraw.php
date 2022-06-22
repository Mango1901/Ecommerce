<?php


namespace Magenest\Affiliate\Model;

use DateTime;
use Exception;
use Magento\Framework\App\Action\Context as ContextAction;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magenest\Affiliate\Helper\Payment;
use Magenest\Affiliate\Model\Withdraw\Status;
use Zend_Serializer_Exception;

/**
 * Class Withdraw
 * @package Magenest\Affiliate\Model
 * @method getPaymentDetails()
 * @method setPaymentDetails($getWithdrawInfoDetail)
 * @method setStatus(int $PENDING)
 * @method getEditRecord()
 * @method getPaymentMethod()
 * @method getStatus()
 * @method getCustomerId()
 * @method setAccount($account)
 * @method getFee()
 * @method setFee(float|int $getFee)
 * @method getAmount()
 * @method getAccount()
 * @method setTransferAmount($transferAmount)
 * @method getTransactionId()
 * @method setTransactionId($getId)
 * @method getAccountId()
 * @method getTransferAmount()
 * @method getCreatedAt()
 * @method setCreatedAt(string $formatTime)
 */
class Withdraw extends AbstractModel
{
    const XML_PATH_EMAIL_ENABLE                     = 'email/withdraw/enable';
    const XML_PATH_WITHDRAW_EMAIL_COMPLETE_TEMPLATE = 'affiliate/email/withdraw/complete';
    /**
     * Cache tag
     * @var string
     */
    const CACHE_TAG = 'affiliate_withdraw';

    /**
     * Cache tag
     * @var string
     */
    protected $_cacheTag = 'affiliate_withdraw';

    /**
     * Event prefix
     * @var string
     */
    protected $_eventPrefix = 'affiliate_withdraw';

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Payment
     */
    protected $_paymentHelper;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @var ResolverInterface
     */
    private $localeResolver;

    /**
     * Withdraw constructor.
     *
     * @param Context $context
     * @param ContextAction $contextAction
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param Payment $helper
     * @param TimezoneInterface $timezone
     * @param ResolverInterface $localeResolver
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context               $context,
        ContextAction         $contextAction,
        Registry              $registry,
        StoreManagerInterface $storeManager,
        Payment               $helper,
        TimezoneInterface     $timezone,
        ResolverInterface     $localeResolver,
        AbstractResource      $resource = null,
        AbstractDb            $resourceCollection = null,
        array                 $data = []
    ) {
        $this->storeManager   = $storeManager;
        $this->messageManager = $contextAction->getMessageManager();
        $this->_paymentHelper = $helper;
        $this->objectManager  = $contextAction->getObjectManager();
        $this->timezone       = $timezone;
        $this->localeResolver = $localeResolver;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\Affiliate\Model\ResourceModel\Withdraw');
    }

    /**
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        if ($this->getPaymentDetails()) {
            $paymentDetail = $this->objectManager->create('Magento\Framework\Json\Helper\Data')
                ->jsonDecode($this->getPaymentDetails());
            $this->addData($paymentDetail);
        }

        return $this;
    }

    /**
     * @return $this|void
     * @throws LocalizedException
     */
    public function beforeSave()
    {
        parent::beforeSave();
        $status = (int)$this->getStatus();
        //set payment method detail
        if ($status !== Status::COMPLETE && $status !== Status::CANCEL) {
            $methodModel = $this->getPaymentModel();
            $this->setPaymentDetails($methodModel->getWithdrawInfoDetail());
        }

        if (!$status) {
            $this->setStatus(Status::PENDING);
        }

        $editWithdraw = $this->getEditRecord();

        if (!$editWithdraw && $this->isObjectNew()) {
            $this->prepareData();
        }
    }

    /**
     * @return mixed
     * @throws Zend_Serializer_Exception
     */
    public function getPaymentModel()
    {
        $paymentModel = $this->_paymentHelper->getMethodModel($this->getPaymentMethod());
        $paymentModel->setData('withdraw', $this);

        return $paymentModel;
    }

    /**
     * @return $this|void
     */
    public function afterSave()
    {
        parent::afterSave();

        if ($this->dataHasChangedFor('status') && ($this->getStatus() == Status::COMPLETE)) {
            $this->formatCreatedAt(2);
            $this->sendWithdrawCompleteEmail();
        }
    }

    /**
     * @return $this
     * @throws LocalizedException
     * @throws Zend_Serializer_Exception
     */
    public function prepareData()
    {
        $account = $this->_paymentHelper->getAffiliateAccount($this->getCustomerId(), 'customer_id');

        $this->setAccount($account)
            ->setAccountId($account->getId());

        $fee = $this->getFee();
        if (empty($fee) && $fee !== '0') {
            $this->setFee($this->_paymentHelper->getFee($this->getPaymentMethod(), $this->getAmount()));
        }

        if (!$this->getTransactionId()) {
            $this->setTransferAmount($this->getAmount());
            $this->setAmount($this->getAmount() + $this->getFee());
            if ($this->getAccount()->getBalance() < $this->getAmount()) {
                throw new LocalizedException(__('The amount request is not enough to withdraw'));
            }

            $transaction = $this->objectManager->create('Magenest\Affiliate\Model\Transaction')
                ->createTransaction('withdraw/create', $this->getAccount(), $this);

            if (!$transaction || !$transaction->getId()) {
                throw new LocalizedException(__('Cannot create transaction for this withdraw.'));
            }

            $this->setTransactionId($transaction->getId());
        }

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function cancel()
    {
        if (!$this->getId()) {
            throw new Exception(
                __('Invalid withdraw data for canceling.')
            );
        }
        $status = (int)$this->getStatus();

        if ($status === Status::CANCEL || $status === Status::COMPLETE) {
            throw new Exception(
                __('Some the withdraw had cancelled.')
            );
        }

        $transaction = $this->objectManager->create('Magenest\Affiliate\Model\Transaction')
            ->load($this->getTransactionId());
        $transaction->cancel();

        $this->setStatus(Status::CANCEL)
            ->save();

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function approve()
    {
        if (!$this->getId() || $this->getStatus() >= Status::COMPLETE) {
            throw new Exception(
                __('Invalid withdraw data for approved.')
            );
        }

        $this->setStatus(Status::COMPLETE)
            ->save();

        return $this;
    }

    /**
     * Get identities
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @param null $status
     *
     * @return mixed
     */
    public function getStatusLabel($status = null)
    {
        if ($status == null) {
            $status = $this->getStatus();
        }
        $statusHash = $this->objectManager->create('\Magenest\Affiliate\Model\Withdraw\Status')->getOptionHash();

        return $statusHash[$status];
    }

    /**
     * @param null $payment
     *
     * @return Phrase|null
     * @throws Zend_Serializer_Exception
     */
    public function getPaymentLabel($payment = null)
    {
        if ($payment == null) {
            $payment = $this->getPaymentMethod();
        }

        $payments = $this->_paymentHelper->getAllMethods();

        if (isset($payments[$payment])) {
            return __($payments[$payment]['label']);
        }

        return $payment;
    }

    /**
     * @return bool
     */
    public function canCancel()
    {
        return $this->getStatus() == Status::PENDING;
    }

    /**
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
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->objectManager->create('\Magento\Customer\Model\Customer')->load($this->getCustomerId());
    }

    /**
     * @return mixed
     */
    public function getPricingHelper()
    {
        return $this->objectManager->create('\Magento\Framework\Pricing\Helper\Data');
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
     * @param $store
     *
     * @return mixed
     */
    public function getFeeAmountFormated($store)
    {
        return $this->getPricingHelper()->currencyByStore($this->getFee(), $store->getId(), true, false);
    }

    /**
     * @param $store
     *
     * @return mixed
     */
    public function getTransferAmountFormated($store)
    {
        return $this->getPricingHelper()->currencyByStore($this->getTransferAmount(), $store->getId(), true, false);
    }

    /**
     * @return $this
     */
    public function sendWithdrawCompleteEmail()
    {
        $account = $this->getAffiliateAccount();
        if (!$this->_paymentHelper->allowSendEmail($account, self::XML_PATH_EMAIL_ENABLE)) {
            return $this;
        }

        $customer = $this->getCustomer();
        if (!$customer || !$customer->getId()) {
            return $this;
        }

        try {
            $this->_paymentHelper->sendEmailTemplate(
                $customer,
                self::XML_PATH_WITHDRAW_EMAIL_COMPLETE_TEMPLATE,
                ['account' => $account, 'withdraw' => $this]
            );
        } catch (LocalizedException $e) {
            $this->_logger->error($e->getMessage());
        }

        return $this;
    }

    /**
     * Get formatted order created date in store timezone
     *
     * @param int $format date format type (\IntlDateFormatter::SHORT|\IntlDateFormatter::MEDIUM
     *                    |\IntlDateFormatter::LONG|\IntlDateFormatter::FULL)
     *
     * @return string
     * @throws Exception
     */
    public function formatCreatedAt($format)
    {
        $formatTime = $this->timezone->formatDateTime(
            new DateTime($this->getCreatedAt()),
            $format,
            $format,
            $this->localeResolver->getDefaultLocale()
        );
        $this->setCreatedAt($formatTime);

        return $this;
    }
}
