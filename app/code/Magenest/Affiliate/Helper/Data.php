<?php

namespace Magenest\Affiliate\Helper;

use Exception;
use Magenest\Affiliate\Model\Account;
use Magenest\Affiliate\Model\AccountFactory;
use Magenest\Affiliate\Model\CampaignFactory;
use Magenest\Affiliate\Model\Config\Source\Urlparam;
use Magenest\Affiliate\Model\Config\Source\Urltype;
use Magenest\Affiliate\Model\TransactionFactory;
use Magenest\Core\Helper\AbstractData;
use Magenest\RewardPoints\Api\GetReferralCodeByCustomerInterface;
use Magento\Backend\Model\Session\Quote;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Cms\Block\Block;
use Magento\Cms\Model\BlockFactory;
use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\State;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Zend\Validator\EmailAddress;
use Zend_Serializer_Exception;

/**
 * Class Data
 * @package Magenest\Affiliate\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'affiliate';
    const AFFILIATE_COOKIE_NAME = 'affiliate_key';
    const AFFILIATE_COOKIE_SOURCE_NAME = 'affiliate_source';
    const AFFILIATE_COOKIE_SOURCE_VALUE = 'affiliate_source_value';
    const XML_PATH_EMAIL_SENDER = 'affiliate/email/sender';
    const CONFIG_PATH_EMAIL = 'email';
    const XML_PATH_EMAIL_SIGN_UP_TEMPLATE = 'affiliate/email/admin/sign_up_template';
    const XML_PATH_EMAIL_WITHDRAW_REQUEST_TEMPLATE = 'affiliate/email/admin/withdraw_request_template';
    const XML_PATH_EMAIL_ACCOUNT_REJECTION_TEMPLATE = 'affiliate/email/account/rejection_template';
    const XML_PATH_EMAIL_ACCOUNT_CHANGE_STATUS_TEMPLATE = 'affiliate/email/account/status_template';
    const XML_PATH_EMAIL_ACCOUNT_WITHDRAW_CANCEL_TEMPLATE = 'affiliate/email/account/withdraw_cancel_template';

    const AFFILIATE_CAMPAIGN_DESCRIPTION = 'affiliate/general/page/campaign_description';
    /**
     * @var array
     */
    protected static $_key = [];
    /**
     * @var array
     */
    private static $_affCache = [];
    /**
     * @var AccountFactory
     */
    protected $accountFactory;
    /**
     * @var CampaignFactory
     */
    protected $campaignFactory;
    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;
    /**
     * @var CustomerFactory
     */
    protected $customerFactory;
    /**
     * @var CustomerSession
     */
    protected $_customerSession;
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;
    /**
     * CookieManager
     * @var CookieManagerInterface
     */
    protected $cookieManager;
    /**
     * @var CookieMetadataFactory
     */
    protected $cookieMetadataFactory;
    /**
     * Block factory
     * @var BlockFactory
     */
    protected $_blockFactory;
    /**
     * @var
     */
    protected $_store;
    /**
     * @var CustomerViewHelper
     */
    protected $customerViewHelper;

    /**
     * @var $_layout
     */
    protected $_layout;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var CheckoutSession
     */
    protected $_checkoutSession;

    /**
     * @var GetReferralCodeByCustomerInterface
     */
    protected $getReferralCodeByCustomer;
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param AccountFactory $accountFactory
     * @param CampaignFactory $campaignFactory
     * @param TransactionFactory $transactionFactory
     * @param BlockFactory $blockFactory
     * @param CustomerFactory $customerFactory
     * @param CookieManagerInterface $cookieManagerInterface
     * @param CustomerSession $customerSession
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
     * @param CustomerViewHelper $customerViewHelper
     * @param LayoutInterface $layout
     * @param Registry $registry
     * @param GetReferralCodeByCustomerInterface $getReferralCodeByCustomer
     */
    public function __construct(
        Context                            $context,
        ObjectManagerInterface             $objectManager,
        AccountFactory                     $accountFactory,
        CampaignFactory                    $campaignFactory,
        TransactionFactory                 $transactionFactory,
        BlockFactory                       $blockFactory,
        CustomerFactory                    $customerFactory,
        CookieManagerInterface             $cookieManagerInterface,
        CustomerSession                    $customerSession,
        CookieMetadataFactory              $cookieMetadataFactory,
        PriceCurrencyInterface             $priceCurrency,
        StoreManagerInterface              $storeManager,
        TransportBuilder                   $transportBuilder,
        CustomerViewHelper                 $customerViewHelper,
        LayoutInterface                    $layout,
        Registry                           $registry,
        GetReferralCodeByCustomerInterface $getReferralCodeByCustomer
    ) {
        $this->_blockFactory = $blockFactory;
        $this->accountFactory = $accountFactory;
        $this->customerFactory = $customerFactory;
        $this->campaignFactory = $campaignFactory;
        $this->transactionFactory = $transactionFactory;
        $this->_customerSession = $customerSession;
        $this->cookieManager = $cookieManagerInterface;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->priceCurrency = $priceCurrency;
        $this->transportBuilder = $transportBuilder;
        $this->customerViewHelper = $customerViewHelper;
        $this->_layout = $layout;
        $this->registry = $registry;
        $this->getReferralCodeByCustomer = $getReferralCodeByCustomer;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * Function used for nice number format
     *
     * @param $number
     *
     * @return float|int
     */
    public static function niceNumberFormat($number)
    {
        return (float)$number === $number ? (int)$number : round($number, 2);
    }

    /** ============================================ General ========================================================
     *
     * @param null $storeId
     *
     * @return mixed
     */
    public function getDefaultPage($storeId = null)
    {
        return $this->getConfigGeneral('page/welcome', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function isOverwriteCookies($storeId = null)
    {
        return $this->getConfigGeneral('overwrite_cookies', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function isDisabledUseCodeAsCoupon($storeId = null)
    {
        return !$this->isUseCodeAsCoupon($storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function isUseCodeAsCoupon($storeId = null)
    {
        return $this->getConfigGeneral('use_code_as_coupon', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function isProcessRefund($storeId = null)
    {
        return $this->getModuleConfig('commission/process/refund', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getExpiredTime($storeId = null)
    {
        return $this->getConfigGeneral('expired_time', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getCustomCss($storeId = null)
    {
        return $this->getConfigGeneral('custom_css', $storeId);
    }

    /** ============================================== Account ========================================================
     *
     * @param null $storeId
     *
     * @return mixed
     */
    public function isEnableTermsAndConditions($storeId = null)
    {
        return $this->getModuleConfig('account/term_conditions/enable', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getAffiliateAccountSignUp($storeId = null)
    {
        return $this->getModuleConfig('account/sign_up', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getTermsAndConditionsTitle($storeId = null)
    {
        return $this->getModuleConfig('account/term_conditions/title', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getTermsAndConditionsHtml($storeId = null)
    {
        return $this->getModuleConfig('account/term_conditions/html', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getTermsAndConditionsCheckboxText($storeId = null)
    {
        return $this->getModuleConfig('account/term_conditions/checkbox_text', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function isCheckedEmailNotification($storeId = null)
    {
        return $this->getModuleConfig('account/term_conditions/default_checkbox', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getDefaultGroup($storeId = null)
    {
        return $this->getModuleConfig('account/sign_up/default_group', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function isAdminApproved($storeId = null)
    {
        return $this->getModuleConfig('account/sign_up/admin_approved', $storeId);
    }

    /** ============================================ Commission ========================================================
     *
     * @param null $storeId
     *
     * @return mixed
     */
    public function isEarnCommissionFromTax($storeId = null)
    {
        return $this->getModuleConfig('commission/by_tax', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function isEarnCommissionFromShipping($storeId = null)
    {
        return $this->getModuleConfig('commission/shipping', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getEarnCommissionInvoiceAfter($storeId = null)
    {
        return $this->getModuleConfig('commission/process/earn_commission_invoice', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function showAffiliateLinkOn($storeId = null)
    {
        return $this->getConfigGeneral('show_link', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getCommissionHoldingDays($storeId = null)
    {
        return $this->getModuleConfig('commission/process/holding_days', $storeId);
    }

    /** ============================================== Withdraw ========================================================
     *
     * @param null $storeId
     *
     * @return mixed
     */
    public function getWithdrawMinimumBalance($storeId = null)
    {
        return (float)$this->getModuleConfig('withdraw/minimum_balance', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return float
     */
    public function getWithdrawMinimum($storeId = null)
    {
        return (float)$this->getModuleConfig('withdraw/minimum', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return float
     */
    public function getWithdrawMaximum($storeId = null)
    {
        return (float)$this->getModuleConfig('withdraw/maximum', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return float
     */
    public function getSafeWithdrawThreshold($storeId = null)
    {
        return (float)$this->getModuleConfig('withdraw/threshold', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function isAllowWithdrawRequest($storeId = null)
    {
        return $this->getModuleConfig('withdraw/allow_request', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getPaymentMethod($storeId = null)
    {
        return $this->getModuleConfig('withdraw/payment_method', $storeId);
    }

    /** ============================================== Refer ========================================================
     *
     * @param null $storeId
     *
     * @return mixed
     */
    public function getReferringPage($storeId = null)
    {
        return $this->getModuleConfig('refer/referring_page', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getAddThisPubId($storeId = null)
    {
        return $this->getModuleConfig('refer/addthis_pubid', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getAddThisClassName($storeId = null)
    {
        return $this->getModuleConfig('refer/addthis_class_name', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getCloudsponge($storeId = null)
    {
        return $this->getModuleConfig('refer/cloudsponge', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getCloudspongeKey($storeId = null)
    {
        return $this->getModuleConfig('refer/cloudsponge_key', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getDefaultEmailSubject($storeId = null)
    {
        return $this->getModuleConfig('refer/sharing_content/subject', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getDefaultEmailBody($storeId = null)
    {
        return $this->getModuleConfig('refer/sharing_content/email_content', $storeId);
    }

    /**
     * Get customer email by order id
     *
     * @param $orderId
     *
     * @return string
     */
    public function getCustomerEmailByOId($orderId)
    {
        $customer_email = '';
        if ($orderId) {
            $order = $this->createObject(Order::class)->load($orderId);
            $customer_email = $order->getCustomerEmail();
        }

        return $customer_email;
    }

    /**
     * Email will be sent or not
     *
     * @param $account
     * @param $xmlEnablePath
     *
     * @return bool
     */
    public function allowSendEmail($account, $xmlEnablePath)
    {
        return $this->getModuleConfig($xmlEnablePath) && $account->getEmailNotification();
    }

    /**
     * @param        $customer
     * @param        $template
     * @param array $templateParams
     * @param string $sender
     * @param null $storeId
     * @param null $email
     *
     * @return $this
     * @throws LocalizedException
     * @throws MailException
     */
    public function sendEmailTemplate(
        $customer,
        $template,
        $templateParams = [],
        $sender = self::XML_PATH_EMAIL_SENDER,
        $storeId = null,
        $email = null
    ) {
        $templateId = $this->scopeConfig->getValue($template, ScopeInterface::SCOPE_STORE, $storeId);
        if ($email === null) {
            $email = $customer->getEmail();
        }

        $templateParams['recipient'] = $customer;

        $transport = $this->transportBuilder->setTemplateIdentifier($templateId)
            ->setTemplateOptions([
                'area' => Area::AREA_FRONTEND,
                'store' => $this->getWebsiteStoreId($customer, $storeId)
            ])
            ->setTemplateVars($templateParams)
            ->setFrom($this->scopeConfig->getValue($sender, ScopeInterface::SCOPE_STORE, $storeId))
            ->addTo($email, $customer->getName())
            ->getTransport();

        $transport->sendMessage();

        return $this;
    }

    /**
     * Get either first store ID from a set website or the provided as default
     *
     * @param      $customer
     * @param null $defaultStoreId
     *
     * @return int|mixed|null
     * @throws LocalizedException
     */
    protected function getWebsiteStoreId($customer, $defaultStoreId = null)
    {
        if (empty($defaultStoreId) && $customer->getWebsiteId() !== 0) {
            $storeIds = $this->storeManager->getWebsite($customer->getWebsiteId())->getStoreIds();
            reset($storeIds);
            $defaultStoreId = current($storeIds);
        }

        if (empty($defaultStoreId)) {
            $defaultStoreId = $this->storeManager->getDefaultStoreView()->getId();
        }

        return $defaultStoreId;
    }

    /**
     * @return string
     */
    public function getCampaignDescriptionBlockId()
    {
        try {
            $code = $this->storeManager->getStore()->getId();
            $scope = ScopeInterface::SCOPE_STORES;
        } catch (Exception $e) {
            $code = null;
            $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        }

        return $this->scopeConfig->getValue(self::AFFILIATE_CAMPAIGN_DESCRIPTION, $scope, $code);
    }

    /**
     * @param      $blockIdentify
     * @param bool $title
     *
     * @return array|string
     */
    public function loadCmsBlock($blockIdentify, $title = false)
    {
        $html = '';
        $titleHtml = '';
        if ($blockIdentify) {
            $block = $this->_blockFactory->create()
                ->load($blockIdentify, 'identifier');
            if ($block->getIsActive()) {
                $titleHtml = $block->getTitle();
                $html = $this->_layout->createBlock(Block::class)->setBlockId($blockIdentify)->toHtml();
            }
        }

        if ($title) {
            return [
                'title' => $titleHtml,
                'content' => $html
            ];
        }

        return $html;
    }

    /**
     * @return null|string
     */
    public function getCustomerReferBy()
    {
        $key = $this->getAffiliateKey();
        $account = $this->accountFactory->create()->loadByCode($key);

        if (!$account->getId()) {
            $account = $this->accountFactory->create()->load($key);
        }

        if ($account->getId()) {
            return $this->getCustomerEmailByAccount($account);
        }

        return null;
    }

    /**
     * Get affiliate key
     * if customer has referred by an other affiliate (has order already), get key from that order
     * else get key from cookie
     * @return null|string
     */
    public function getAffiliateKey()
    {
        $key = $this->getAffiliateKeyFromCookie();
        if ($this->hasFirstOrder()) {
            $key = $this->getFirstAffiliateOrder()->getAffiliateKey();
        }

        return $key;
    }

    /**
     * @param null $key
     *
     * @return mixed
     */
    public function getAffiliateKeyFromCookie($key = null)
    {
        if ($key === null) {
            $key = self::AFFILIATE_COOKIE_NAME;
        }

        if (!isset(self::$_key[$key])) {
            self::$_key[$key] = $this->cookieManager->getCookie($key);
        }

        return self::$_key[$key];
    }

    /**
     * Check customer has referred or not
     * @return bool
     */
    public function hasFirstOrder()
    {
        $firstOrder = $this->getFirstAffiliateOrder();

        return $firstOrder && $firstOrder->getId();
    }

    /**
     * Get first order which has been referred by an affiliate
     * @return mixed
     */
    public function getFirstAffiliateOrder()
    {
        $cacheKey = 'affiliate_first_order';
        if (!self::hasCache($cacheKey)) {
            $customer = $this->getCustomer();
            if ($customer && $customer->getId()) {
                $order = $this->createObject(Order::class)
                    ->getCollection()
                    ->addFieldToFilter('customer_id', $customer->getId());

                self::saveCache($cacheKey, $order->getFirstItem());
            }
        }

        return self::getCache($cacheKey);
    }

    /**
     * @param $cacheKey
     *
     * @return bool
     */
    public static function hasCache($cacheKey)
    {
        if (isset(self::$_affCache[$cacheKey])) {
            return true;
        }

        return false;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->_customerSession->getCustomer();
    }

    /**
     * @param      $cacheKey
     * @param null $value
     */
    public static function saveCache($cacheKey, $value = null)
    {
        self::$_affCache[$cacheKey] = $value;
    }

    /**
     * @param $cacheKey
     *
     * @return mixed|null
     */
    public static function getCache($cacheKey)
    {
        if (isset(self::$_affCache[$cacheKey])) {
            return self::$_affCache[$cacheKey];
        }

        return null;
    }

    /**
     * @param $account
     *
     * @return string
     */
    public function getCustomerEmailByAccount($account)
    {
        $customerId = '';
        if (is_object($account)) {
            $customerId = $account->getCustomerId();
        } else {
            $account = $this->accountFactory->create()->load($account);
            if ($account->getId()) {
                $customerId = $account->getCustomerId();
            }
        }

        $customer = $this->customerFactory->create()->load($customerId);
        if ($customer->getId()) {
            return $customer->getEmail();
        }

        return '';
    }

    /**
     * @param $input
     *
     * @return Account
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getAffiliateByEmailOrCode($input)
    {
        /** @var Account $account */
        $account = $this->accountFactory->create();

        $validator = new EmailAddress();
        if ($validator->isValid($input)) {
            $websiteId = $this->storeManager->getStore()->getWebsiteId();
            $customer = $this->customerFactory->create();
            $customer->setWebsiteId($websiteId)->loadByEmail($input);
            if ($customer && $customer->getId()) {
                $account->loadByCustomer($customer);
            }
        } else {
            $account->loadByCode($input);
        }

        return $account;
    }

    /**
     * @return $this
     * @throws FailureToSendException
     * @throws InputException
     */
    public function deleteAffiliateCookieSourceName()
    {
        $this->deleteAffiliateKeyFromCookie();
        $this->deleteAffiliateKeyFromCookie(self::AFFILIATE_COOKIE_SOURCE_NAME);

        return $this;
    }

    /**
     * @param null $key
     *
     * @return $this
     * @throws InputException
     * @throws FailureToSendException
     */
    public function deleteAffiliateKeyFromCookie($key = null)
    {
        if ($key === null) {
            $key = self::AFFILIATE_COOKIE_NAME;
        }

        if ($this->cookieManager->getCookie($key)) {
            $this->cookieManager->deleteCookie(
                $key,
                $this->cookieMetadataFactory
                    ->createCookieMetadata()
                    ->setPath('/')
                    ->setDomain(null)
            );
        }

        self::$_key[$key] = null;

        return $this;
    }

    /**
     * @param null $key
     *
     * @return mixed
     */
    public function getAffiliateSourceFromCookie($key = null)
    {
        if ($key === null) {
            $key = self::AFFILIATE_COOKIE_SOURCE_NAME;
        }

        if (!isset(self::$_key[$key])) {
            self::$_key[$key] = $this->cookieManager->getCookie($key);
        }

        return self::$_key[$key];
    }

    /**
     * @param null $key
     *
     * @return mixed
     */
    public function getAffiliateSourceValueFromCookie($key = null)
    {
        if ($key === null) {
            $key = self::AFFILIATE_COOKIE_SOURCE_VALUE;
        }

        if (!isset(self::$_key[$key])) {
            self::$_key[$key] = $this->cookieManager->getCookie($key);
        }

        return self::$_key[$key];
    }

    /**
     * @param null $url
     * @param array $params
     * @param null $urlType
     *
     * @return string
     */
    public function getSharingUrl($url = null, $params = [], $urlType = null)
    {
        $url = $url ?: $this->getDefaultReferLink();
        if (!$url) {
            $url = $this->_urlBuilder->getUrl('affiliate/index/index');
        }

        $param = $this->getSharingParam($params, $urlType);

        return trim($url, '/') . $param;
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getDefaultReferLink($storeId = null)
    {
        return $this->getModuleConfig(
            'refer/default_link',
            $storeId
        ) ?: $this->_urlBuilder->getUrl('affiliate/index/index');
    }

    /**
     * @param array $params
     * @param null $urlType
     *
     * @return string
     */
    public function getSharingParam($params = [], $urlType = null)
    {
        $prefix = $this->getUrlPrefix() ?: 'u';
        $urlType = $urlType ?: $this->getUrlType();
        $accountCode = $this->getCurrentAffiliate()->getCode();

        if ($this->getGeneralUrlParam() === Urlparam::PARAM_ID) {
            $accountCode = $this->getCurrentAffiliate()->getId();
        }

        if (!$accountCode && $this->_customerSession->getCustomerId()) {
            $accountCode = $this->getReferralCodeByCustomer->execute($this->_customerSession->getCustomerId());
        }

        if ($urlType === Urltype::TYPE_HASH) {
            $param = '#' . $prefix . $accountCode;

            return $param;
        }

        $params[$prefix] = $accountCode;
        $param = '';
        foreach ($params as $key => $code) {
            $paramPrefix = ($param !== '') ? '&' : '?';
            $param .= $paramPrefix . $key . '=' . urlencode($code);
        }

        return $param;
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getUrlPrefix($storeId = null)
    {
        return $this->getConfigGeneral('url/prefix', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getUrlType($storeId = null)
    {
        return $this->getConfigGeneral('url/type', $storeId);
    }

    /**
     * @return mixed
     */
    public function getCurrentAffiliate()
    {
        $customerId = $this->_customerSession->getCustomerId();

        return $this->getAffiliateAccount($customerId, 'customer_id');
    }

    /**
     * @param      $value
     * @param null $code
     *
     * @return mixed
     */
    public function getAffiliateAccount($value, $code = null)
    {
        if ($code) {
            $account = $this->accountFactory->create()->load($value, $code);
        } else {
            $account = $this->accountFactory->create()->load($value);
        }

        return $account;
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getGeneralUrlParam($storeId = null)
    {
        return $this->getConfigGeneral('url/param', $storeId);
    }

    /**
     * @param       $router
     * @param array $param
     *
     * @return string
     */
    public function getAffiliateUrl($router, $param = [])
    {
        return $this->_getUrl($router, $param);
    }

    /**
     * @return PriceCurrencyInterface
     */
    public function getPriceCurrency()
    {
        return $this->priceCurrency;
    }

    /**
     * @return Phrase
     */
    public function getCreditTitle()
    {
        $account = $this->getCurrentAffiliate();

        return __('My Credit (%1)', $this->formatPrice($account->getBalance()));
    }

    /**
     * @param $price
     *
     * @return string
     */
    public function formatPrice($price)
    {
        return $this->priceCurrency->convertAndFormat($price, false);
    }

    /**
     * @param $affiliateDiscount
     *
     * @return $this
     * @throws LocalizedException
     */
    public function saveAffiliateDiscount($affiliateDiscount)
    {
        $affDiscountData = $this->getAffiliateDiscount();
        $this->getCheckoutSession()->setAffDiscountData(array_merge($affDiscountData, $affiliateDiscount));

        return $this;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getAffiliateDiscount()
    {
        $affDiscountData = $this->getCheckoutSession()->getAffDiscountData();
        if (!is_array($affDiscountData) || $this->hasFirstOrder()) {
            $affDiscountData = [];
        }

        return $affDiscountData;
    }

    /**
     * @return mixed
     * @throws LocalizedException
     */
    public function getCheckoutSession()
    {
        if (!$this->_checkoutSession) {
            $this->_checkoutSession = $this->objectManager->get(
                $this->isAdmin()
                    ? Quote::class
                    : CheckoutSession::class
            );
        }

        return $this->_checkoutSession;
    }

    /**
     * @return bool
     * @throws LocalizedException
     */
    public function isAdmin()
    {
        /** @var State $state */
        $state = $this->objectManager->get('Magento\Framework\App\State');

        return $state->getAreaCode() === Area::AREA_ADMINHTML;
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function getAffiliateByKeyOrCode($key)
    {
        $account = $this->accountFactory->create()->loadByCode($key);
        if (!$account->getId()) {
            $account = $this->accountFactory->create()->load($key);
        }

        return $account;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @throws
     */
    public function resetAffiliate($quote)
    {
        /**
         * @var State $state
         */
        $state = $this->objectManager->get('Magento\Framework\App\State');

        $this->setAffiliateKeyToCookie(null);
        $this->setAffiliateKeyToCookie('coupon', null);

        if ($state->getAreaCode() === 'graphql') {
            return $this;
        }

        $quote->setAffiliateKey(null);
        $quote->setAffiliateDiscountAmount(null);
        $quote->setBaseAffiliateDiscountAmount(null);
        $quote->setAffiliateCommission(null);
        $quote->setDiscountCustomerAffiliate(null);
        $quote->setBaseDiscountCustomerAffiliate(null);
        $quote->save();
    }

    /**
     * @param      $value
     * @param null $key
     *
     * @return $this
     * @throws InputException
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     */
    public function setAffiliateKeyToCookie($value, $key = null)
    {
        $expirationDay = (int)$this->getConfigGeneral('expired_time');
        $period = $expirationDay * 24 * 3600;
        if ($key === null) {
            $key = self::AFFILIATE_COOKIE_NAME;
        }

        if ($this->cookieManager->getCookie($key)) {
            $this->cookieManager->deleteCookie(
                $key,
                $this->cookieMetadataFactory
                    ->createCookieMetadata()
                    ->setPath('/')
                    ->setDomain(null)
            );
        }

        $this->cookieManager->setPublicCookie(
            $key,
            $value,
            $this->cookieMetadataFactory
                ->createPublicCookieMetadata()
                ->setDuration($period)
                ->setPath('/')
                ->setDomain(null)
        );

        self::$_key[$key] = $value;

        return $this;
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function isEnableReferFriend($store = null)
    {
        return $this->getModuleConfig('refer/enable', $store);
    }

    /**
     * @param $object
     *
     * @return null|string
     * @throws Zend_Serializer_Exception
     */
    public function getSerializeString($object)
    {
        if ($object === null) {
            return null;
        }

        return $this->serialize($object);
    }

    /**
     * @param $object
     *
     * @return mixed|null
     * @throws Zend_Serializer_Exception
     */
    public function getArrayUnserialize($object)
    {
        if ($object === null) {
            return null;
        }

        return $this->unserialize($object);
    }

    /**
     * @param $string
     *
     * @return mixed
     * @throws Zend_Serializer_Exception
     */
    public function unserialize($string)
    {
        if ($string) {
            return parent::unserialize($string);
        }

        return [];
    }

    /**
     * @param $fieldset
     * @param $prefix
     * @param $url
     * @param $action
     */
    public function addCustomerEmailFieldset($fieldset, $prefix, $url, $action)
    {
        $fieldset->addField('customer_email', 'text', [
            'label' => __('Account'),
            'name' => 'customer_email',
            'required' => true,
            'readonly' => true,
            'style' => 'background-color:white;opacity: 1;cursor: pointer; '
        ])->setAfterElementHtml(
            '<div id="customer-grid" style="display:none"></div>
                <script type="text/x-magento-init">
                    {
                        "#' . $prefix . '_customer_email": {
                            "Magenest_Affiliate/js/customer":{
                                "url": "' . $url . '",
                                "prefix" : "' . $prefix . '",
                                "action" : "' . $action . '"
                            }
                        }
                    }
                </script>'
        );
    }

    /**
     * Retrieve banner redirect URL for Affiliate Pro
     *
     * @param $key
     *
     * @return mixed
     */
    public function getBannerRedirectUrl($key)
    {
        return $this->objectManager->create(
            'Magenest\Affiliate\Model\Banner'
        )->load($key)->getLink();
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getAdminEmails($storeId = null)
    {
        return $this->getEmailConfig('admin/emails_to', $storeId);
    }

    /********************************** Email Configuration *********************
     *
     * @param      $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getEmailConfig($code = '', $storeId = null)
    {
        $code = $code ? self::CONFIG_PATH_EMAIL . '/' . $code : self::CONFIG_PATH_EMAIL;

        return $this->getModuleConfig($code, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function isEnableAffiliateSignUpEmail($storeId = null)
    {
        return $this->isEnabled($storeId) && $this->getEmailConfig('admin/enable_sign_up', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function isEnableWithdrawRequestEmail($storeId = null)
    {
        return $this->isEnabled($storeId) && $this->getEmailConfig('admin/enable_withdraw_request', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function isEnableAccountRejectionEmail($storeId = null)
    {
        return $this->isEnabled($storeId) && $this->getEmailConfig('account/enable_rejection', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function isEnableAccountChangeStatusEmail($storeId = null)
    {
        return $this->isEnabled($storeId) && $this->getEmailConfig('account/enable_status', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function isEnableWithdrawCancelEmail($storeId = null)
    {
        return $this->isEnabled($storeId) && $this->getEmailConfig('account/enable_withdraw_cancel', $storeId);
    }

    /**
     * @param $searchCriteria
     * @param $searchResult
     *
     * @return mixed
     */
    public function processGetList($searchCriteria, $searchResult)
    {
        if ($this->versionCompare('2.2.0')) {
            /** @var CollectionProcessorInterface $collectionProcessor */
            $collectionProcessor = $this->objectManager->get(
                '\Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface'
            );

            /** @var JoinProcessorInterface $joinProcessor */
            $joinProcessor = $this->objectManager->get(
                '\Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface'
            );

            $collectionProcessor->process($searchCriteria, $searchResult);
            $joinProcessor->process($searchResult);
        } else {
            foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
                $this->addFilterGroupToCollection($filterGroup, $searchResult);
            }

            $sortOrders = $searchCriteria->getSortOrders();
            if ($sortOrders === null) {
                $sortOrders = [];
            }
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $field = $sortOrder->getField();
                $searchResult->addOrder(
                    $field,
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }

            $searchResult->setCurPage($searchCriteria->getCurrentPage());
            $searchResult->setPageSize($searchCriteria->getPageSize());
        }

        $searchResult->setSearchCriteria($searchCriteria);

        return $searchResult;
    }

    /**
     * @return int|mixed
     */
    public function getCodeLength()
    {
        $length = $this->getUrlCodeLength();
        if (is_nan($length) || $length <= 0) {
            $length = 6;
        }
        if ($length > 32) {
            $length = 32;
        }

        return $length;
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getUrlCodeLength($storeId = null)
    {
        return $this->getConfigGeneral('url/code_length', $storeId);
    }

    public function getCustomerEmailByOrder($orderId)
    {
        $order = $this->objectManager->create('Magento\Sales\Model\Order')->load($orderId);
        $customer_email = $order->getCustomerEmail();
        if ($customer_email) {
            return $customer_email;
        }

        return null;
    }

    public function getCommissionFee($storeId = null)
    {
        return $this->getModuleConfig('commission/process/fee', $storeId);
    }

    /**
     * @param $customerId
     * @return mixed|null
     */
    public function getAccountIdByCustomerId($customerId)
    {
        try {
            $account = $this->accountFactory->create()->load($customerId, 'customer_id');
            if ($account->getStatus() == Account\Status::ACTIVE) {
                return $account->getId();
            }
        } catch (Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
        return null;
    }
}
