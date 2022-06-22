<?php

namespace Magenest\RewardPoints\Helper;

use Exception;
use Magenest\RewardPoints\Api\Data\MembershipInterface;
use Magenest\RewardPoints\Model\AccountFactory;
use Magenest\RewardPoints\Model\Coupon\MassgeneratorFactory;
use Magenest\RewardPoints\Model\ExpiredFactory;
use Magenest\RewardPoints\Model\LifetimeAmountFactory;
use Magenest\RewardPoints\Model\ReferralFactory;
use Magenest\RewardPoints\Model\RuleFactory;
use Magenest\RewardPoints\Model\ResourceModel\Rule as RuleResource;
use Magenest\RewardPoints\Model\Rule;
use Magenest\RewardPoints\Model\TransactionFactory;
use Magento\Backend\App\ConfigInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory;
use Magento\CatalogRule\Model\RuleFactory as CoreRuleFactory;
use Magento\Checkout\Model\Session;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Serialize;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\ResourceModel\Quote as QuoteResource;
use Magento\Quote\Model\ResourceModel\Quote\Item;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Zend_Json;

/**
 * Class Data
 *
 * @package Magenest\RewardPoints\Helper
 */
class Data extends AbstractHelper
{
    const XML_PATH_MODULE_ENABLE = 'rewardpoints/reward_points_display/reward_points_enable';

    const XML_PATH_CONFIG_PRODUCT_LIST_ENABLE = 'rewardpoints/view_setting/product_list_enable';
    const XML_PATH_CONFIG_HOME_PAGE_ENABLE = 'rewardpoints/view_setting/home_page_product_list_enable';
    const XML_PATH_CONFIG_PRODUCT_DETAIL_ENABLE = 'rewardpoints/view_setting/product_detail_enable';
    const XML_PATH_CONFIG_SHOW_POINT_FOR_GUEST = 'rewardpoints/view_setting/show_point_for_guest';

    const XML_PATH_POINT_UNIT = 'rewardpoints/label_display_setting/suffix';
    const XML_PATH_POINT_SIZE = 'rewardpoints/label_display_setting/text_size';
    const XML_PATH_POINT_COLOR = 'rewardpoints/label_display_setting/text_color';

    const XML_PATH_POINT_BASE_MONEY = 'rewardpoints/point_config/points_money';
    const XML_PATH_EARN_APPLY_POINT = 'rewardpoints/point_config/earn_apply_point';
    const XML_PATH_EARN_APPLY_DISCOUNT = 'rewardpoints/point_config/earn_apply_discount';
    const XML_PATH_POINT_SUBTRACT_DISCOUNT = 'rewardpoints/point_config/subtract_in_discount';
    const XML_PATH_DEDUCT_POINT_REFUND = 'rewardpoints/point_config/deduct_automatically';
    const XML_PATH_POINT_ROUNDING = 'rewardpoints/point_config/up_or_down';
    const POINT_ROUNDING_UP = 'up';
    const POINT_ROUNDING_DOWN = 'down';
    const XML_PATH_POINT_TIME_EXPIRE = 'rewardpoints/point_config/points_time_expired';
    const XML_PATH_ENABLE_REWARD_POINTS_LOGIN_NOFITICATION = 'rewardpoints/point_config/reward_points_noti';

    const XML_PATH_SPENDING_CONFIGURATION_ENABLE = 'rewardpoints/spending_configuration/spending_configuration_enable';
    const XML_PATH_SPENDING_POINT = 'rewardpoints/spending_configuration/spending_point';
    const SPENDING_POINT_FIXED_VALUE = 1;
    const SPENDING_POINT_PERCENTAGE_VALUE = 2;
    const XML_PATH_MAXIMUM_POINT = 'rewardpoints/spending_configuration/maximum_point';
    const XML_PATH_PERCENT_MAX_ORDER = 'rewardpoints/spending_configuration/percent_max_order';

    const XML_PATH_NOTITY_CUSTOMERS_WHEN_THEY_LOGIN = 'rewardpoints/email_configuration/point_expiration_notification/notity_customers_when_they_login';
    const XML_PATH_POINT_EXPIRATION_EMAIL_ENABLE = 'rewardpoints/email_configuration/point_expiration_notification/point_expiration_email_enable';
    const XML_PATH_POINT_EXPIRATION_TEMPLATE = 'rewardpoints/email_configuration/point_expiration_notification/points_expiration_template';
    const XML_PATH_SENDER_BEFORE_NOTIFY = 'rewardpoints/email_configuration/point_expiration_notification/send_before_notify';
    const XML_PATH_EMAIL_BALANCE_ENABLE = 'rewardpoints/email_configuration/update_point_balance_email/email_balance_enable';
    const XML_PATH_UPDATE_BALANCE_TEMPLATE = 'rewardpoints/email_configuration/update_point_balance_email/update_balance_template';

    const XML_PATH_REFER_BY_LINK = 'rewardpoints/referafriend/referral_code/refer_by_link';
    const XML_PATH_REFER_PATH = 'rewardpoints/referafriend/referral_code/refer_path';
    const XML_PATH_REFERRAL_CODE_CODE_PATTERN = 'rewardpoints/referafriend/referral_code/code_pattern';

    const XML_PATH_REFERRAL_EARNING_TYPE = 'rewardpoints/referafriend/referral_setting/referral_person_earner';
    const XML_PATH_REFERRAL_COUPON_AWARDED_TO = 'rewardpoints/referafriend/referral_setting/referral_coupon_person_earner';
    const XML_PATH_REFERRAL_WHEN_COUPON_SENT_REFERED = 'rewardpoints/referafriend/referral_setting/when_send_coupon_refered';
    const XML_PATH_REFERRAL_WHEN_COUPON_SENT_REFERRER = 'rewardpoints/referafriend/referral_setting/when_send_coupon_referrer';
    const XML_PATH_REFERRAL_EMAIL_TEMPLATE_REFERRED = 'rewardpoints/referafriend/referral_setting/send_coupon_for_refered';
    const XML_PATH_REFERRAL_EMAIL_TEMPLATE_REFERRER = 'rewardpoints/referafriend/referral_setting/send_coupon_for_referrer';
    const XML_PATH_REFERRAL_SHOPPING_CART_RULE_REFERED = 'rewardpoints/referafriend/referral_setting/coupon_for_refered';
    const XML_PATH_REFERRAL_SHOPPING_CART_RULE_REFERRER = 'rewardpoints/referafriend/referral_setting/coupon_for_referrer';

    const XML_PATH_AFFILIATE_APPLY_COUPON_AFFILIATE = 'rewardpoints/point_config/apply_coupon_for_affiliate';
    const XML_PATH_AFFILIATE_SHOPPING_CART_RULE_AFFILIATE = 'rewardpoints/point_config/coupon_for_affiliate';
    const XML_PATH_AFFILIATE_EMAIL_TEMPLATE_AFFILIATE = 'rewardpoints/point_config/send_coupon_for_affiliate';

    const XML_PATH_BIRTHDAY_APPLY_COUPON_BIRTHDAY = 'rewardpoints/point_config/apply_coupon_for_birthday';
    const XML_PATH_BIRTHDAY_SHOPPING_CART_RULE_BIRTHDAY = 'rewardpoints/point_config/coupon_for_birthday';
    const XML_PATH_BIRTHDAY_EMAIL_TEMPLATE_BIRTHDAY = 'rewardpoints/point_config/send_coupon_for_birthday';

    const XML_PATH_TIER_ENABLE = 'rewardpoints/reward_tiers/reward_tiers_enable';
    const XML_PATH_TIER_DISCOUNT = 'rewardpoints/reward_tiers/discount';
    const NOT_UPDATE = 0;
    const UPDATE = 1;
    const INVOICED_AMOUNT = 1;
    const ORDERED_AMOUNT = 2;
    //Merger Module ReferAFriend

    const XML_PATH_MEMBERSHIP_STATUS = 'rewardpoints/membership/membership_status';

    const XML_PATH_MEMBERSHIP_DESCRIPTION = 'rewardpoints/membership/reward_points_membership_description';

    const REWARD_POINT_FOR_ORDER = -10;
    private static $messagplusmoneywhenordering = "Points are added for every order: ";

    private static $messageminuspointswhenordering = "Spend points for purchasing order: ";

    const POINTS_VOIDED_AT_ORDER_REFUND = -4;
    private static $messageDeductPointRefund = "Points voided at order refund: ";

    const POINTS_RETURN_WHEN_REFUND = -5;
    private static $messageReturnPointRefund = "Return applied reward points for order when refund: ";

    const POINT_BY_REFUNDED = -1;
    private static $messageRefundToGiftCard = "We refund the bonus for the order:";

    const ADD_POINTS_BY_TRANSACTION = -11;
    private static $generalpointmessage = "Add points by transaction:";

    const EXPIRY_POINT = -12;
    private static $messagepointsexpired = "Expiry point";

    const POINT_ADDED_BY_ADMIN = -13;
    private static $adminmessagesgivepoints = "Point added by admin";

    const REWARD_REFERER = -2;
    private static $Referercode = "Referer code";

    const REWARD_MEMBERSHIP = -14;
    const REWARD_MEMBERSHIP_MESSAGE = 'Rewards for Member Tier';

    const XML_PATH_VALID_STATUSES = 'rewardpoints/point_config/valid_used_statuses';

    const XML_PATH_ENABLE_CONVERT = 'rewardpoints/convert/enabled';
    const XML_PATH_RATE_CONVERT = 'rewardpoints/convert/rate';

    /**
     * @var AccountFactory
     */
    protected $_accountFactory;

    /**
     * @var TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * @var RuleFactory
     */
    protected $_ruleFactory;

    /**
     * @var ExpiredFactory
     */
    protected $_expiredFactory;

    /**
     * @var CoreRuleFactory
     */
    protected $_coreRuleFactory;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var ProductFactory
     */
    protected $_productFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $_customersFactory;

    /**
     * @var Configurable
     */
    protected $_configurable;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var CurrentCustomer
     */
    protected $_currentCustomer;

    /**
     * @var \Magenest\RewardPoints\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * @var CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var CurrencyInterface
     */
    protected $localeCurrency;

    /**
     * @var LifetimeAmountFactory
     */
    protected $lifetimeAmountFactory;

    /**
     * @var CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var AttributeFactory
     */
    protected $eavAttributeFactory;

    /**
     * @var TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * @var Pool
     */
    protected $cacheFrontendPool;

    protected $productRules;
    protected $connection;
    protected $websiteId;
    protected $validProductRules;

    /**
     * @var Serialize
     */
    protected $serialize;

    protected $_quote;

    /**
     * @var Email
     */
    protected $email;

    //Merger module ReferAFriend
    /**
     * @var Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var ReferralFactory
     */
    protected $referralFactory;

    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var MassgeneratorFactory
     */
    protected $massgeneratorFactory;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var MembershipAction
     */
    protected $_membershipAction;

    /**
     * @var null
     */
    private $pointsEarn = null;

    /**
     * @var RuleResource
     */
    protected $ruleResource;

    /**
     * @var ProductResource
     */
    protected $productResource;

    /**
     * @var QuoteResource
     */
    protected $quoteResource;

    /**
     * @var \Magenest\RewardPoints\Model\ResourceModel\Account\CollectionFactory
     */
    protected $accountCollectionFactory;

    /**
     * @var \Magenest\RewardPoints\Model\ResourceModel\Expired\CollectionFactory
     */
    protected $expiredCollectionFactory;

    /**
     * Data constructor.
     * @param MembershipAction $membershipAction
     * @param Context $context
     * @param AccountFactory $accountFactory
     * @param CoreRuleFactory $coreRuleFactory
     * @param StoreManagerInterface $storeManagerInterface
     * @param ProductFactory $productFactory
     * @param Configurable $configurable
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customersFactory
     * @param TransactionFactory $transactionFactory
     * @param CustomerFactory $customerFactory
     * @param QuoteFactory $quoteFactory
     * @param OrderFactory $orderFactory
     * @param RuleFactory $ruleFactory
     * @param ExpiredFactory $expiredFactory
     * @param CurrentCustomer $currentCustomer
     * @param RuleResource\CollectionFactory $ruleCollectionFactory
     * @param CollectionFactory $orderCollectionFactory
     * @param ConfigInterface $config
     * @param CurrencyInterface $localeCurrency
     * @param LifetimeAmountFactory $lifetimeAmountFactory
     * @param CurrencyFactory $currencyFactory
     * @param ResourceConnection $resource
     * @param Registry $registry
     * @param AttributeFactory $eavAttributeFactory
     * @param TypeListInterface $cacheTypeList
     * @param Pool $cacheFrontendPool
     * @param Serialize $serialize
     * @param Email $email
     * @param \Magento\Customer\Model\Session $customerSession
     * @param ReferralFactory $referralFactory
     * @param ProductMetadataInterface $productMetadata
     * @param MassgeneratorFactory $massgeneratorFactory
     * @param Session $checkoutSession
     * @param RuleResource $ruleResource
     * @param ProductResource $productResource
     * @param QuoteResource $quoteResource
     * @param \Magenest\RewardPoints\Model\ResourceModel\Account\CollectionFactory $accountCollectionFactory
     */
    public function __construct(
        MembershipAction $membershipAction,
        Context $context,
        AccountFactory $accountFactory,
        CoreRuleFactory $coreRuleFactory,
        StoreManagerInterface $storeManagerInterface,
        ProductFactory $productFactory,
        Configurable $configurable,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customersFactory,
        TransactionFactory $transactionFactory,
        CustomerFactory $customerFactory,
        QuoteFactory $quoteFactory,
        OrderFactory $orderFactory,
        RuleFactory $ruleFactory,
        ExpiredFactory $expiredFactory,
        CurrentCustomer $currentCustomer,
        \Magenest\RewardPoints\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory,
        CollectionFactory $orderCollectionFactory,
        ConfigInterface $config,
        CurrencyInterface $localeCurrency,
        LifetimeAmountFactory $lifetimeAmountFactory,
        CurrencyFactory $currencyFactory,
        ResourceConnection $resource,
        Registry $registry,
        AttributeFactory $eavAttributeFactory,
        TypeListInterface $cacheTypeList,
        Pool $cacheFrontendPool,
        Serialize $serialize,
        Email $email,
        \Magento\Customer\Model\Session $customerSession,
        ReferralFactory $referralFactory,
        ProductMetadataInterface $productMetadata,
        MassgeneratorFactory $massgeneratorFactory,
        Session $checkoutSession,
        RuleResource $ruleResource,
        ProductResource $productResource,
        QuoteResource $quoteResource,
        \Magenest\RewardPoints\Model\ResourceModel\Account\CollectionFactory $accountCollectionFactory,
        \Magenest\RewardPoints\Model\ResourceModel\Expired\CollectionFactory $expiredCollectionFactory
    ) {
        $this->_configurable = $configurable;
        $this->_ruleFactory = $ruleFactory;
        $this->_expiredFactory = $expiredFactory;
        $this->orderFactory = $orderFactory;
        $this->quoteFactory = $quoteFactory;
        $this->_accountFactory = $accountFactory;
        $this->_coreRuleFactory = $coreRuleFactory;
        $this->_customersFactory = $customersFactory;
        $this->_transactionFactory = $transactionFactory;
        $this->customerFactory = $customerFactory;
        $this->_productFactory = $productFactory;
        $this->_storeManager = $storeManagerInterface;
        $this->_currentCustomer = $currentCustomer;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->config = $config;
        $this->localeCurrency = $localeCurrency;
        $this->lifetimeAmountFactory = $lifetimeAmountFactory;
        $this->currencyFactory = $currencyFactory;
        $this->resource = $resource;
        $this->registry = $registry;
        $this->eavAttributeFactory = $eavAttributeFactory;
        $this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
        $this->serialize = $serialize;
        $this->email = $email;
        //merger Module ReferAFriend
        $this->moduleManager = $context->getModuleManager();
        $this->customerSession = $customerSession;
        $this->referralFactory = $referralFactory;
        $this->productMetadata = $productMetadata;
        $this->massgeneratorFactory = $massgeneratorFactory;
        $this->checkoutSession = $checkoutSession;
        $this->_membershipAction = $membershipAction;
        $this->ruleResource = $ruleResource;
        $this->productResource = $productResource;
        $this->quoteResource = $quoteResource;
        $this->accountCollectionFactory = $accountCollectionFactory;
        $this->expiredCollectionFactory = $expiredCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Get store configuration value
     *
     * @param $path
     *
     * @return mixed
     */
    public function getConfigData($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed|string
     */
    public function getRewardPointsWhen()
    {
        return $this->getConfigData(self::XML_PATH_VALID_STATUSES) ?? 'processing,complete';
    }

    public function getPointColor()
    {
        return $this->getConfigData(self::XML_PATH_POINT_COLOR);
    }

    public function getPointSize()
    {
        return $this->getConfigData(self::XML_PATH_POINT_SIZE);
    }

    public function getPointUnit()
    {
        $pointUnit = $this->getConfigData(self::XML_PATH_POINT_UNIT) ?? 'P';

        return __($pointUnit);
    }

    public function getEnableModule()
    {
        return $this->getConfigData(self::XML_PATH_MODULE_ENABLE);
    }

    public function getTimeExpired()
    {
        return $this->getConfigData(self::XML_PATH_POINT_TIME_EXPIRE);
    }

    public function getExpiryType($transactionId)
    {
        $expiredModel = $this->_expiredFactory->create();
        $expiredModel = $expiredModel->load($transactionId, 'transaction_id');
        if (!$expiredModel->getId()) return 0;
        $expiryType = $expiredModel->getExpiryType();
        if ($expiryType === null) {
            $timeExpired = $this->getTimeExpired();
            return (bool)$timeExpired;
        }

        return $expiryType;
    }

    public function getDiscountInfo()
    {
        return $this->getConfigData(self::XML_PATH_TIER_DISCOUNT);
    }

    public function getRewardTiersEnable()
    {
        return $this->getConfigData(self::XML_PATH_TIER_ENABLE);
    }

    public function getUpOrDown()
    {
        return $this->getConfigData(self::XML_PATH_POINT_ROUNDING);
    }

    //Start upgrade module

    public function getSpendingConfigurationEnable()
    {
        return $this->getConfigData(self::XML_PATH_SPENDING_CONFIGURATION_ENABLE);
    }

    public function getSpendingPoint()
    {
        return $this->getConfigData(self::XML_PATH_SPENDING_POINT);
    }

    public function getMaximumPoint()
    {
        return $this->getConfigData(self::XML_PATH_MAXIMUM_POINT);
    }

    public function getPercentMaxOrder()
    {
        return $this->getConfigData(self::XML_PATH_PERCENT_MAX_ORDER);
    }

    public function getSubscribeDefault()
    {
        return $this->getConfigData(self::XML_PATH_NOTITY_CUSTOMERS_WHEN_THEY_LOGIN);
    }

    public function getSenderBeforeNotify()
    {
        return $this->getConfigData(self::XML_PATH_SENDER_BEFORE_NOTIFY);
    }

    public function getBalanceEmailEnable()
    {
        return $this->getConfigData(self::XML_PATH_EMAIL_BALANCE_ENABLE);
    }

    public function getBalanceTemplate()
    {
        return $this->getConfigData(self::XML_PATH_UPDATE_BALANCE_TEMPLATE);
    }

    public function getExpirationEmailEnable()
    {
        return $this->getConfigData(self::XML_PATH_POINT_EXPIRATION_EMAIL_ENABLE);
    }

    public function getSendBefore()
    {
        return $this->getConfigData(self::XML_PATH_SENDER_BEFORE_NOTIFY);
    }

    public function getExpirationTemplate()
    {
        return $this->getConfigData(self::XML_PATH_POINT_EXPIRATION_TEMPLATE);
    }

    //end upgrade mudule

    /**
     * @return string
     */
    public function getMagentoVersion()
    {
        return $this->productMetadata->getVersion();
    }

    /**
     * @return mixed
     */
    public function getCanEarnPointWithAppliedPoints()
    {
        $result = $this->getConfigData(self::XML_PATH_EARN_APPLY_POINT);

        return (bool)$result;
    }

    /**
     * @return mixed
     */
    public function getCanEarnPointWithAppliedDiscount()
    {
        $result = $this->getConfigData(self::XML_PATH_EARN_APPLY_DISCOUNT);

        return (bool)$result;
    }

    /**
     * @return bool
     */
    public function isShowProductDetailEnabled()
    {
        if ($this->getConfigData(self::XML_PATH_CONFIG_PRODUCT_DETAIL_ENABLE) == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function isShowProductListEnabled()
    {
        if ($this->getConfigData(self::XML_PATH_CONFIG_PRODUCT_LIST_ENABLE) == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function isShowPointInHomePageEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_CONFIG_HOME_PAGE_ENABLE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool|mixed
     */
    public function getEnableReferralCodes()
    {
        if (!$this->getEnableModule()) {
            return false;
        } else {
            return $this->getConfigData(self::XML_PATH_MODULE_ENABLE);
        }
    }

    /**
     * @param $point
     *
     * @return float|int
     */
    public function getRewardBaseAmount($point = null)
    {
        $value = $this->getConfigData(self::XML_PATH_POINT_BASE_MONEY);
        if ($point === null) {
            return $value;
        }
        return $point / $value;
    }

    /**
     * @return bool
     */
    public function getSubtractPoint()
    {
        $value = $this->getConfigData(self::XML_PATH_POINT_SUBTRACT_DISCOUNT);

        if (!$this->getCanEarnPointWithAppliedPoints() && !$this->getCanEarnPointWithAppliedDiscount()) {
            $value = 0;
        }

        return (bool)$value;
    }

    /**
     * @param $ruleId
     * @param $websiteId
     * @param $productId
     *
     * @return array|mixed
     */
    public function getMatchingViewProductIds($ruleId, $websiteId, $productId)
    {
        $ruleModel = $this->_ruleFactory->create();
        $this->ruleResource->load($ruleModel, $ruleId);
        $product = $this->_productFactory->create();
        $this->productResource->load($product, $productId);
        return $this->getMatchingViewProductIdsByModel($ruleModel, $websiteId, $product);
    }

    /**
     * @param $rule
     * @param $websiteId
     * @param $product
     * @return array|mixed
     */
    public function getMatchingViewProductIdsByModel($rule, $websiteId, $product)
    {
        $ruleId = $rule->getId();
        $productId = $product->getEntityId();
        $registryObject = $this->registry;

        $ids = [];
        if ($ruleId != 0 && isset($registryObject)) {
            $ids = $registryObject->registry('RulesId' . $ruleId . 'productId' . $productId);
            if (empty($ids)) {
                /** @var \Magento\CatalogRule\Model\Rule $coreRule */
                $coreRule = $this->_coreRuleFactory->create();
                $conditions = $rule->getData('conditions_serialized');
                $coreRule->setConditionsSerialized($conditions);
                $coreRule->setWebsiteIds($websiteId);
                $coreRule->setIsActive(1);
                $ids = [];

                $websites = $this->_getWebsitesMap();

                $results = [];
                foreach ($websites as $_websiteId => $defaultStoreId) {
                    $product->setStoreId($defaultStoreId);
                    $results[$_websiteId] = $coreRule->getConditions()->validate($product);
                }

                if (!empty($results[$websiteId])) {
                    array_push($ids, $productId);
                }

                $registryObject->unregister('RulesId' . $ruleId . 'productId' . $productId);
                $registryObject->register('RulesId' . $ruleId . 'productId' . $productId, $ids);
            }
        }
        return $ids;
    }

    /**
     * Display point on catalog view
     *
     * @param $productId
     *
     * @return array
     * @throws LocalizedException
     */
    public function getProductRule($productId)
    {
        $productModel = $this->_productFactory->create()->load($productId);

        return $this->getProductRuleByModel($productModel);
    }

    /**
     * Display point on catalog view
     *
     * @param $product
     * @return array
     * @throws LocalizedException
     */
    public function getProductRuleByModel($product)
    {
        $result = [];
        $childrenDatas = [];
        if ($product->getTypeId() == 'configurable') {
            $productChildrenArray = $product->getTypeInstance()->getUsedProducts($product);
            $attributeConfigurableIdArray = $product->getTypeInstance()->getConfigurableOptions($product);
            $optionCodeArray = [];

            $attributeConfigurableIdArrayKeys = [];
            foreach ($attributeConfigurableIdArray as $key => $value) {
                $attributeConfigurableIdArrayKeys[] = $key;
            }
            $eavAttributeCollection = $this->eavAttributeFactory->create()->getCollection()
                ->addFieldToFilter('attribute_id', ["in" => $attributeConfigurableIdArrayKeys]);
            foreach ($eavAttributeCollection as $value) {
                $attributeCode = $value->getAttributeCode();
                $optionCodeArray[] = $attributeCode;
            }

            /**
             * @var Product $productChildren
             */
            foreach ($productChildrenArray as $productChildren) {
                $childrenDataElement['id'] = $productChildren->getEntityId();
                foreach ($optionCodeArray as $optionCode) {
                    $optionCodeValue = $productChildren->getData($optionCode);
                    $childrenDataElement[$optionCode] = $optionCodeValue;
                }
                $childrenDataElement['points'] = $this->getProductRuleByModel($productChildren);
                $childrenDatas[$childrenDataElement['id']] = $childrenDataElement;
            }
            $result = $childrenDatas;
        } else {
            $rules = $this->getValidProductRule();
            if (!empty($rules)) {
                $websiteId = $this->getWebsiteId();
                foreach ($rules as $rule) {
                    if ($this->validateRule($rule)) {
                        $ruleProductIds = $this->getMatchingViewProductIdsByModel($rule, $websiteId, $product);
                        if ($ruleProductIds) {
                            $result[] = ['point' => $rule->getPoints(), 'steps' => $rule->getSteps()];
                        }
                    }
                }
            }
        }

        return $result;
    }

    public function getConnection()
    {
        if ($this->connection === null) {
            $this->connection = $this->resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        }

        return $this->connection;
    }

    public function getWebsiteId()
    {
        if ($this->websiteId === null) {
            $this->websiteId = $this->_storeManager->getWebsite()->getId();
        }
        return $this->websiteId;
    }

    public function getValidProductRule()
    {
        if ($this->validProductRules === null) {
            $ruleArr = [];
            $rules = $this->ruleCollectionFactory->create()->addFieldToFilter('rule_type', Rule::RULE_TYPE_PRODUCT);
            foreach ($rules as $rule) {
                if ($this->validateRule($rule)) {
                    $ruleArr[] = $rule;
                }
            }
            $this->validProductRules = $ruleArr;
        }

        return $this->validProductRules;
    }

    /**
     * Get total points earned from quote
     *
     * @param $quote Quote
     *
     * @return float
     * @throws LocalizedException
     */
    public function getPointsEarn($quote)
    {
        if ($this->pointsEarn === null) {
            if ($this->getEnableModule()) {
                $totalPoints = 0;
                if (!$this->getCanEarnPointWithAppliedPoints() && intval($quote->getData('reward_point')) > 0) {
                    return $totalPoints;
                }
                if (isset($quote)) {
                    if (!$this->getCanEarnPointWithAppliedDiscount() && $quote->getCouponCode()) {
                        return $totalPoints;
                    }
                }
                $isSubtract = $this->getSubtractPoint();
                $subtractPercent = 0;
                if ($isSubtract) {
                    $rewardPoint = intval($quote->getData('reward_point'));
                    $rewardAmount = $this->getRewardBaseAmount($rewardPoint);
                    if ((float)$quote->getSubtotalWithDiscount() === 0.00 || (float)$quote->getGrandTotal() === 0.00) {
                        return 0;
                    }
                    $subtractPercent = $rewardAmount / $quote->getSubtotalWithDiscount();
                }
                /** @var Item $item */
                foreach ($quote->getAllItems() as $item) {
                    $rowTotal = $item->getRowTotal();
                    $price = $item->getPrice() * $item->getQty();
                    if ($rowTotal != 0 && $price != 0) {
                        $total = $rowTotal / $price;
                    } else {
                        $total = self::UPDATE;
                    }
                    $totalPoints = $this->checkPoint($item, $rowTotal, $subtractPercent, $total, $totalPoints);
                }

                // add first purchase point
                $totalPoints += $this->getTotalPointsFirstPurchase($quote);

                // add lifetime amount point
                $totalPoints += $this->getPointsLifetimeAmount($quote);

                if ($this->getUpOrDown() == 'up') {
                    $totalPoints = ceil($totalPoints);
                } else {
                    $totalPoints = floor($totalPoints);
                }
                $this->pointsEarn = $totalPoints;
            } else {
                $this->pointsEarn = 0;
            }
        }

        return $this->pointsEarn;
    }

    /**
     * @param $quote
     * @return float|int|mixed|null
     * @throws LocalizedException
     */
    public function getPointsEarnIncludeMembershipReward($quote)
    {
        $pointsEarn = $this->getPointsEarn($quote);
        return $pointsEarn + $this->getMembershipReward($quote);
    }

    /**
     * @param $quote
     * @return float|int|mixed
     * @throws LocalizedException
     */
    public function getMembershipReward($quote)
    {
        if ($quote->getCustomerId()) {
            $pointsEarn = $this->getPointsEarn($quote);
            return $this->_membershipAction->setCustomerId($quote->getCustomerId())->applyAdditionalEarningPoints($pointsEarn);
        }
        return 0;
    }

    /**
     * Get point earned of each item in quote
     *
     * @param $item
     * @param $subtractPercent
     * @param bool $ruleIds
     *
     * @return float|int
     * @throws LocalizedException
     */
    public function getQuoteItemPoints($item, $subtractPercent, $ruleIds = false)
    {
        $productId = $item->getProductId();
        $rules = $this->ruleCollectionFactory->create()->addFieldToFilter('rule_type', Rule::RULE_TYPE_PRODUCT);
        $product = $this->_productFactory->create()->load($productId);
        $productType = $product->getTypeId();
        if ($productType == 'bundle') {
            return 0;
        } else {
            $point = 0;

            if ($this->getSubtractPoint()) {
                $parentItemId = $item->getParentItemId();
                $rowTotal = $item->getRowTotal();
                if ($parentItemId != null && $rowTotal != 0) {
                    $price = $item->getPrice() * $item->getQty();
                    $total = $rowTotal / $price;
                    $finalPrice = $item->getPrice() - ($item->getDiscountAmount() / ($item->getQty() * $total));
                    $finalPrice -= $finalPrice * $subtractPercent;
                } else {
                    $finalPrice = $item->getPrice() - ($item->getDiscountAmount() / $item->getQty());
                    $finalPrice -= $finalPrice * $subtractPercent;
                }
            } else {
                $finalPrice = $item->getPrice();
            }
            if ($finalPrice < 0.00) {
                $finalPrice = 0;
            }
            foreach ($rules as $rule) {
                $ruleId = $rule->getId();
                if ($ruleIds) {
                    if ($ruleId !== $ruleIds) {
                        continue;
                    }
                }
                if ($this->validateRule($rule)) {
                    $websiteId = $this->_storeManager->getWebsite()->getId();
                    $ruleProductIds = $this->getMatchingViewProductIds($ruleId, $websiteId, $productId);
                    if ($ruleProductIds) {
                        if ($rule->getActionType() == 1 && $productType !== 'configurable') {
                            $point += $rule->getPoints();
                        } elseif ($rule->getActionType() == 2) {
                            $step = $rule->getSteps();
                            $base = $rule->getPoints();
                            $cartSubtotal = $item->getPrice() * $item->getQty();
                            if ($cartSubtotal >= $step) {
                                $point += round(($finalPrice / $step) * $base, 2);
                            }
                        }
                    }
                }
            }
            if ($this->getUpOrDown() == 'up') {
                $totalPoints = ceil($point);
            } else {
                $totalPoints = floor($point);
            }
            return $totalPoints;
        }
    }

    /**
     * Get point for category view
     *
     * @param $productId
     * @param null $item
     *
     * @return array|float|int
     * @throws LocalizedException
     */
    public function getProductPoints($productId, $item = null)
    {
        $product = $this->_productFactory->create()->load($productId);
        $productType = $product->getTypeId();
        if ($productType == 'bundle') {
            $count = 0;
            $productChildrenPointBundleArray = [];
            $productChildrenBundleIds = $product->getTypeInstance()->getChildrenIds($productId, true);
            foreach ($productChildrenBundleIds as $productChildrenBundleId) {
                $productChildrenPointArray = [];
                foreach ($productChildrenBundleId as $productChildrenId) {
                    $productChildrenPoint = $this->getProductPoints($productChildrenId);
                    $productChildrenPointArray[$count] = $productChildrenPoint;
                    $count++;
                }
                $productChildrenPointBundleArray[] = $productChildrenPointArray;
            }

            return $productChildrenPointBundleArray;
        } elseif ($productType == 'grouped') {
            $productChildrenGroups = $product->getTypeInstance()->getAssociatedProducts($product);
            $groupedProductPoints = [];
            foreach ($productChildrenGroups as $child) {
                $childId = $child->getEntityId();
                if (!$productId) {
                    continue;
                }

                if (!empty($pointEarn = $this->getProductPoints($childId))) {
                    $groupedProductPoints[] = $pointEarn;
                }
            }
            if (empty($groupedProductPoints)) {
                return 0;
            }
            return min($groupedProductPoints);
        } elseif ($productType == 'configurable') {
            $childsProduct = $product->getTypeInstance()->getUsedProducts($product);

            $configPoints = [];
            foreach ($childsProduct as $childProduct) {
                if (!empty($pointEarn = $this->getProductPoints($childProduct->getEntityId()))) {
                    $configPoints[] = $pointEarn;
                }
            }

            return empty($configPoints) ? 0 : min($configPoints);
        } else {
            if ($product->getTypeId() == 'configurable' and $item) {
                $children = $item->getChildren();
                foreach ($children as $child) {
                    $point += $this->getProductPoints($child->getProductId());
                }

                return $point;
            }
            if ($item) {
                $finalPrice = $item->getPrice();
            } else {
                $rate = $this->_storeManager->getStore()->getBaseCurrency()->getRate($this->_storeManager->getStore()->getCurrentCurrency()->getCode());
                if (!$rate) {
                    $rate = 1;
                }
                $finalPrice = $product->getPriceInfo()->getPrice('final_price')->getValue() / $rate;//final price in base currency
            }

            return $this->getPointFromProductPrice($finalPrice, $productId);
        }
    }

    /**
     * @param $finalPrice
     * @param $productId
     * @return false|float|int
     * @throws LocalizedException
     */
    public function getPointFromProductPrice($finalPrice, $productId)
    {
        $point = 0;
        $rules = $this->ruleCollectionFactory->create()->addFieldToFilter('rule_type', Rule::RULE_TYPE_PRODUCT);
        foreach ($rules as $rule) {
            $ruleId = $rule->getId();
            if ($this->validateRule($rule)) {
                $websiteId = $this->_storeManager->getWebsite()->getId();
                $ruleProductIds = $this->getMatchingViewProductIds($ruleId, $websiteId, $productId);
                if ($ruleProductIds) {
                    if ($rule->getActionType() == 1) {
                        $point += $rule->getPoints();
                    } elseif ($rule->getActionType() == 2) {
                        $step = $rule->getSteps();
                        $base = $rule->getPoints();
                        if ($finalPrice >= $step) {
                            if ($this->getUpOrDown() == 'up') {
                                $point += ceil(round(($finalPrice / $step) * $base, 2));
                            } else {
                                $point += floor(($finalPrice / $step) * $base);
                            }
                        }
                    }
                }
            }
        }
        return $point;
    }

    /**
     * Validate rule object
     * @param $rule
     *
     * @return bool
     */
    public function validateRule($rule)
    {
        $today = date('Y-m-d');
        $status = $rule->getStatus();
        $fromDate = $rule->getFromDate();
        $toDate = $rule->getToDate();
        if ($fromDate) {
            $inFromDate = strtotime($today) >= strtotime($fromDate) ? true : false;
        } else {
            $inFromDate = true;
        }

        if ($toDate) {
            $inToDate = strtotime($today) <= strtotime($toDate) ? true : false;
        } else {
            $inToDate = true;
        }

        if (($status == '1') && $inFromDate && $inToDate) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Earn point when saving order
     *
     * @param $order
     *
     * @throws LocalizedException
     * @throws Exception
     */
    public function earnOrderPoints($order)
    {
        $quote = $this->quoteFactory->create(); //to load orders created from backend correctly
        $this->quoteResource->load($quote, $order->getQuoteId());
        $orderId = $order->getId();
        $customerId = $order->getCustomerId();
        $rules = $this->ruleCollectionFactory->create()->addFieldToFilter('rule_type', Rule::RULE_TYPE_PRODUCT);
        // Return if earning points with discount isn't enabled
        if (!$this->canEarnPointsWithDiscount($quote)) {
            return;
        }

        if (!empty($rules)) {
            foreach ($rules as $rule) {
                $point = 0;
                $ruleId = $rule->getId();
                $transaction = $this->findTransaction($customerId, $ruleId, $orderId, null);

                if ($this->validateRule($rule) && $transaction->getId() == null) {
                    $isSubtract = $this->getSubtractPoint();
                    $needCalculate = true;
                    $subtractPercent = 0;
                    if ($isSubtract) {
                        $rewardPoint = intval($quote->getData('reward_point'));
                        $rewardAmount = $this->getRewardBaseAmount($rewardPoint);
                        if ((float)$quote->getSubtotalWithDiscount() === 0.00 || (float)$quote->getGrandTotal() === 0.00) {
                            $point = 0;
                            $needCalculate = false;
                        }
                        $subtractPercent = $rewardAmount / $quote->getSubtotalWithDiscount();
                    }
                    if ($needCalculate) {
                        if ($quote->isMultipleShippingAddresses()) {
                            $point += $this->getQuoteMultiShipping($order, $quote, $subtractPercent, $ruleId);
                        } else {
                            $point += $this->getQuoteAllItemsPoints($quote, $subtractPercent, $ruleId);
                        }
                    }
                    $this->addAdditionalPointsForMembership($customerId, $point);

                    // Update transaction history for reward points
                    $this->updateTransactionHistory($order, $ruleId, $point);
                }
            }
        }
    }

    /**
     * @param $quote
     * @param $subtractPercent
     * @param $ruleId
     * @return float|int
     * @throws LocalizedException
     */
    public function getQuoteAllItemsPoints($quote, $subtractPercent, $ruleId)
    {
        $point = 0;
        foreach ($quote->getAllItems() as $item) {
            $rowTotal = $item->getRowTotal();
            $price = $item->getPrice() * $item->getQty();
            if ($rowTotal != 0 && $price != 0) {
                $total = $rowTotal / $price;
            } else {
                $total = self::UPDATE;
            }
            $point = $this->checkPoint($item, $rowTotal, $subtractPercent, $total, $point, $ruleId);
        }
        return $point;
    }

    /**
     * @param $order
     * @param $ruleId
     * @param $point
     * @throws Exception
     */
    public function updateTransactionHistory($order, $ruleId, $point)
    {
        if ($point == 0) {
            return;
        }
        $orderModel = $this->orderFactory->create();
        $orderId = $order->getId();
        $customerId = $order->getCustomerId();

        $comment = 'Order #: ' . $orderModel->load($orderId)->getIncrementId();
        $accountModel = $this->_accountFactory->create();
        $account = $this->accountCollectionFactory->create()->addFieldToFilter('customer_id', $customerId)->getFirstItem();
        if ($account->getId()) {
            $accountModel->load($account->getId());
        }
        $data = [
            'customer_id' => $customerId,
            'points_total' => $account->getData('points_total') + $point,
            'points_current' => $account->getData('points_current') + $point,
        ];
        $accountModel->addData($data)->save();

        $transactionModel = $this->_transactionFactory->create();
        $data = [
            'rule_id' => $ruleId,
            'order_id' => $orderId,
            'customer_id' => $customerId,
            'points_change' => $point,
            'points_after' => $accountModel->getData('points_current'),
            'comment' => $comment
        ];
        $transactionModel->addData($data)->save();

        //Send email
        if ($this->getBalanceEmailEnable()) {
            $rule_id = -10;
            $this->getSendEmail($order, $account, $point, $rule_id, null, null);
        }

        $transactionId = $transactionModel->getId();
        /**
         * @param $expiredModel ExpiredFactory
         */
        if ($point > 0) {
            $expiredModel = $this->_expiredFactory->create();
            $timeToExpired = (int)$this->getTimeExpired();
            $timeExpired = strtotime("+" . $timeToExpired . " days");
            $dateExpired = date("Y-m-d H:i:s", $timeExpired);
            $data = [
                'transaction_id' => $transactionId,
                'rule_id' => $ruleId,
                'order_id' => $orderId,
                'customer_id' => $customerId,
                'points_change' => $point,
                'expired_date' => $dateExpired,
                'status' => 'available',
                'expiry_type' => (bool)$timeToExpired
            ];
            $expiredModel->addData($data)->save();
        }
    }

    /**
     * @param $order
     * @throws Exception
     */
    public function earnOrderPointsBehavior($order)
    {
        $quote = $this->quoteFactory->create()->load($order->getQuoteId()); //to load orders created from backend correctly
        $customerId = $order->getCustomerId();
        $rules = $this->ruleCollectionFactory->create()->addFieldToFilter('rule_type', Rule::RULE_TYPE_BEHAVIOR);
        // Return if earning points with discount isn't enabled
        if (!$this->canEarnPointsWithDiscount($quote)) {
            return;
        }

        foreach ($rules as $rule) {
            $point = 0;
            $ruleId = $rule->getId();

            $point += $this->processFirstPurchaseRule($rule, $order, $quote);
            $point += $this->processLifetimeAmountCondition($rule, $order, $quote);

            $isSubtract = $this->getSubtractPoint();
            if ($isSubtract) {
                if ((float)$quote->getSubtotalWithDiscount() === 0.00 || (float)$quote->getGrandTotal() === 0.00) {
                    $point = 0;
                }
            }

            if (!empty($point)) {
                $this->addAdditionalPointsForMembership($customerId, $point);
                // Update transaction history for reward points
                $this->updateTransactionHistory($order, $ruleId, $point);
            }
        }
    }

    /**
     * @param $rule
     * @param $order
     * @param $quote
     * @return int
     */
    protected function processFirstPurchaseRule($rule, $order, $quote)
    {
        $point = 0;
        $ruleId = $rule->getId();
        $condition = $rule->getCondition();
        $transaction = $this->findTransaction($order->getCustomerId(), $ruleId, $order->getId(), null);
        $isFirstPurchase = $this->validateFirstOrder($quote, 1);

        // First Purchase Rule
        if ($isFirstPurchase && $condition == Rule::CONDITION_FIRST_PURCHASE && $this->validateRule($rule) && $transaction->getId() == null) {
            $point += $this->validateMinSubtotal($rule, $quote);
        }

        return $point;
    }

    /**
     * @param $rule
     * @param $order
     * @param $quote
     * @return int
     */
    protected function processLifetimeAmountCondition($rule, $order, $quote)
    {
        $point = 0;
        $ruleId = $rule->getId();
        $condition = $rule->getCondition();
        $transaction = $this->findTransaction($order->getCustomerId(), $ruleId, $order->getId(), null);

        if (($condition == Rule::CONDITION_LIFETIME_AMOUNT) && $this->validateRule($rule) && $transaction->getId() == null) {
            // add point from lifetime amount rule
            $lifetimeConfig = $this->getLifetimeAmountConfig($rule);

            if (isset($lifetimeConfig['type']) && $lifetimeConfig['type'] == self::ORDERED_AMOUNT) {
                $point += $this->validateLifetimeAmount($rule, $quote);
            }
        }

        return $point;
    }

    /**
     * @param $customerId
     * @param $point
     * @param $transactionId
     *
     * @param int $ruleId
     * @return bool|mixed
     * @throws NoSuchEntityException
     */
    public function addPointsAccount($customerId, $point, $transactionId, $ruleId = self::POINT_ADDED_BY_ADMIN)
    {
        $accountModel = $this->_accountFactory->create();
        $account = $this->accountCollectionFactory->create()->addFieldToFilter('customer_id', $customerId)->getFirstItem();
        if ($account->getId()) {
            $accountModel->load($account->getId());
        }
        if ($account->getData('points_current') + $point <= 0) {
            $point = -$account->getData('points_current');
            if ($point == 0) {
                return 0;
            }
        }
        $data = [
            'customer_id' => $customerId,
            'store_id' => $this->_storeManager->getStore()->getId(),
            'points_total' => $account->getData('points_total') + $point,
            'points_current' => $account->getData('points_current') + $point,
        ];
        $accountModel->addData($data)->save();

        if ($point > 0) {
            $expiredModel = $this->_expiredFactory->create();
            $timeToExpired = (int)$this->getTimeExpired();
            $timeExpired = strtotime("+" . $timeToExpired . " days");
            $dateExpired = date("Y-m-d H:i:s", $timeExpired);
            $data = [
                'transaction_id' => $transactionId,
                'rule_id' => '-1',
                'customer_id' => $customerId,
                'points_change' => $point,
                'expired_date' => $dateExpired,
                'status' => 'available',
                'expiry_type' => (bool)$timeToExpired
            ];

            //Send email
            if ($this->getBalanceEmailEnable()) {
                $ruleTitle = '.';
                $order = $this->customerFactory->create()->load($customerId);
                $this->getSendEmail($order, $account, $point, $ruleId, $ruleTitle, null);
            }

            $expiredModel->addData($data)->save();
        } else {
            $expiredModel = $this->_expiredFactory->create();
            $listPointOfUser = $expiredModel->getCollection()
                ->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('status', 'available')
                ->setOrder('expired_date', 'ASC')->getData();

            $neededPoint = -$point;
            if (is_array($listPointOfUser)) {
                if ($neededPoint > 0) {
                    foreach ($listPointOfUser as $userPoint) {
                        $pointId = $userPoint['id'];
                        $details = $expiredModel->load($pointId);
                        $detail = $details->getData();
                        if ($neededPoint >= $detail['points_change']) {
                            $neededPoint -= $detail['points_change'];
                            $detail['status'] = 'used';
                            $details->setData($detail)->save();
                        } else {
                            $detail['points_change'] -= $neededPoint;
                            $details->setData($detail)->save();
                            break;
                        }
                    }
                }
            } else {
                $pointId = $listPointOfUser['id'];
                $details = $expiredModel->load($pointId);
                $detail = $details->getData();
                $detail['points_change'] -= $neededPoint;
                $details->setData($detail)->save();
            }
        }

        return $accountModel->getData('points_current');
    }

    /**
     * Add point for behavior rules
     *
     * @param $customerId
     * @param $ruleId
     * @param $orderId
     * @param $productId
     *
     * @return bool|int
     * @throws Exception
     */
    public function addPoints($customer, $ruleId, $orderId, $productId)
    {
        $point = 0;
        $customerId = $customer->getId();
        $rule = $this->_ruleFactory->create()->load($ruleId);
        $conditionType = $rule->getCondition();
        $transaction = $this->findTransaction($customerId, $ruleId, $orderId, $productId, $conditionType);
        if ($transaction->getId() && $rule->getCondition() != 'review') {
            return $point;
        }
        $point = $rule->getPoints();
        if ($point == 0) {
            return $point;
        }
        if ($productId != 0) {
            $product = $this->_productFactory->create()->load($productId);
            $comment = 'Product Name: ' . $product->getName();
        } else {
            $comment = null;
        }
        $this->addAdditionalPointsForMembership($customerId, $point);

        $accountModel = $this->_accountFactory->create();
        $account = $this->accountCollectionFactory->create()->addFieldToFilter('customer_id', $customerId)->getFirstItem();
        if ($account->getId()) {
            $accountModel->load($account->getId());
        }

        $data = [
            'customer_id' => $customerId,
            'store_id' => $this->_storeManager->getStore()->getId(),
            'points_total' => $account->getData('points_total') + $point,
            'points_current' => $account->getData('points_current') + $point,
        ];
        $accountModel->addData($data)->save();
        $transactionModel = $this->_transactionFactory->create();
        $data = [
            'rule_id' => $ruleId,
            'order_id' => $orderId,
            'customer_id' => $customerId,
            'points_change' => $point,
            'points_after' => $accountModel->getData('points_current'),
            'product_id' => $productId,
            'comment' => $comment
        ];
        $transactionModel->addData($data)->save();
        $transactionId = $transactionModel->getId();

        if ($point > 0) {
            $expiredModel = $this->_expiredFactory->create();
            $timeToExpired = (int)$this->getTimeExpired();
            $timeExpired = strtotime("+" . $timeToExpired . " days");
            $dateExpired = date("Y-m-d H:i:s", $timeExpired);
            $data = [
                'transaction_id' => $transactionId,
                'rule_id' => $ruleId,
                'order_id' => $orderId,
                'customer_id' => $customerId,
                'points_change' => $point,
                'expired_date' => $dateExpired,
                'status' => 'available',
                'expiry_type' => (bool)$timeToExpired
            ];

            if ($this->getBalanceEmailEnable()) {
                $rule_id = -11;
                $ruleTitle = $rule->getTitle();
                $this->getSendEmail(null, $account, $point, $rule_id, $ruleTitle, $customer);
            }

            $expiredModel->addData($data)->save();
        }

        return true;
    }

    /**
     * @param $customerId
     * @param $ruleId
     * @param $orderId
     * @param $productId
     * @param null $conditionType
     * @return DataObject
     */
    public function findTransaction($customerId, $ruleId, $orderId, $productId, $conditionType = null)
    {
        if ($orderId) {
            $transaction = $this->_transactionFactory->create()->getCollection()
                ->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('rule_id', $ruleId)
                ->addFieldToFilter('order_id', $orderId)
                ->getFirstItem();
        } else {
            if ($conditionType && $conditionType == Rule::CONDITION_LOGIN_DAILY) {
                $today          = new \DateTime();
                $todayNotTime       = $today->format('Y-m-d');

                $transaction = $this->_transactionFactory->create()->getCollection()
                    ->addFieldToFilter('customer_id', $customerId)
                    ->addFieldToFilter('rule_id', $ruleId)
                    ->addFieldToFilter('insertion_date',  ['like' => "%$todayNotTime%"])
                    ->getFirstItem();
            } else {
                $transaction = $this->_transactionFactory->create()->getCollection()
                    ->addFieldToFilter('customer_id', $customerId)
                    ->addFieldToFilter('rule_id', $ruleId)
                    ->addFieldToFilter('product_id', [['eq' => $productId], ['null' => true]])
                    ->addFieldToFilter('order_id', [['eq' => $orderId], ['null' => true]])
                    ->getFirstItem();
            }

        }

        return $transaction;
    }

    /**
     * Get discount amount when reaching reward tier
     *
     * @param $points
     *
     * @return int
     */
    public function getDiscountPercent($points)
    {
        $discount = 0;
        $checkData = $this->getDiscountInfo();
        if (isset($checkData)) {
            $discountInfo = $this->serialize->unserialize($checkData);
            if ($discountInfo) {
                foreach ($discountInfo as $info) {
                    if ($points >= $info['points'] && $discount <= $info['discount']) {
                        $discount = $info['discount'];
                    }
                }
            }
        } else {
            return null;
        }
        return $discount;
    }

    /**
     * @param $customerId
     * @param $ruleId
     * @param $orderId
     * @param $productId
     *
     * @return bool
     * @throws Exception
     */
    public function removePoints($customerId, $ruleId, $orderId, $productId)
    {
        $transaction = $this->findTransaction($customerId, $ruleId, $orderId, $productId);
        if (!$transaction->getId()) {
            return false;
        } else {
            $rule = $this->_ruleFactory->create();
            $this->ruleResource->load($rule, $ruleId);
            $point = $rule->getPoints();
            $accountModel = $this->_accountFactory->create();
            $account = $this->accountCollectionFactory->create()->addFieldToFilter('customer_id', $customerId)->getFirstItem();
            if ($account->getId()) {
                $accountModel->load($account->getId());
            }
            if ($account->getData('points_current') < $point) {
                return false;
            }
            $data = [
                'customer_id' => $customerId,
                'points_total' => $account->getData('points_total') - $point,
                'points_current' => $account->getData('points_current') - $point,
            ];
            $accountModel->addData($data)->save();
            $expiredModel = $this->_expiredFactory->create();
            $listPointOfUser = $this->expiredCollectionFactory->create()
                ->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('status', 'available')
                ->setOrder('expired_date', 'ASC')
                ->getData();
            $neededPoint = $point;
            if (is_array($listPointOfUser)) {
                foreach ($listPointOfUser as $userPoint) {
                    if ($neededPoint > 0) {
                        $pointId = $userPoint['id'];
                        $details = $expiredModel->load($pointId);
                        $detail = $details->getData();
                        if ($neededPoint >= $detail['points_change']) {
                            $neededPoint -= $detail['points_change'];
                            $detail['status'] = 'used';
                            $details->setData($detail)->save();
                        } else {
                            $detail['points_change'] -= $neededPoint;
                            $details->setData($detail)->save();
                            break;
                        }
                    } else {
                        break;
                    }
                }
            } else {
                $pointId = $listPointOfUser['id'];
                $details = $expiredModel->load($pointId);
                $detail = $details->getData();
                $detail['points_change'] -= $neededPoint;
                $details->setData($detail)->save();
            }

            $transaction->delete();

            return true;
        }
    }

    /**
     * @return string $rewardPointHtml
     */
    public function getCurrentPoints()
    {
        $rewardPointHtml = '';
        $customerId = $this->_currentCustomer->getCustomerId();

        if (!$customerId) {
            return $rewardPointHtml;
        } else {
            $account = $this->_accountFactory->create()
                ->getCollection()
                ->addFieldToFilter('customer_id', $customerId)
                ->getFirstItem();
            if ($account) {
                $currentPoint = $account->getPointsCurrent();
                if ($currentPoint == null) {
                    $currentPoint = 0;
                }
                $rewardPointHtml .= '(' . $currentPoint . ' P)';
            }
        }

        return $rewardPointHtml;
    }

    /**
     * @return array
     */
    protected function _getWebsitesMap()
    {
        $map = [];
        $websites = $this->_storeManager->getWebsites();
        foreach ($websites as $website) {
            // Continue if website has no store to be able to create catalog rule for website without store
            if ($website->getDefaultStore() === null) {
                continue;
            }
            $map[$website->getId()] = $website->getDefaultStore()->getId();
        }

        return $map;
    }

    /**
     * Check if module Magenest_RewardPoints is enabled
     *
     * @return bool
     */
    public static function isReferAFriendModuleEnabled()
    {
        return true;
    }

    /**
     * todo
     * Validate if number of orders of customer is less than limit or not
     *
     * @param $quote
     * @param $limit
     * @return bool
     */
    public function validateFirstOrder($quote, $limit)
    {
        $customerId = $quote->getCustomerId();
        if (!$customerId) {
            return false;
        }

        $orderCollection = $this->orderCollectionFactory->create()->addFieldToFilter('customer_id', $customerId);
        if ($orderCollection->getSize() > $limit) {
            return false;
        }

        return true;
    }

    /**
     * Validate minimum value of subtotal
     * If valid, return points
     * If not valid, return null
     *
     * @param $rule
     * @param $quote
     */
    public function validateMinSubtotal($rule, $quote)
    {
        $count = 0;
        /** @var Item $item */
        foreach ($quote->getAllItems() as $item) {
            $valid = $rule->getConditions()->validate($item);
            if ($valid) {
                $count++;
            }
        }
        if ($count) {
            return $rule->getPoints();
        }

        return;
    }

    /**
     * Get total points from all first purchase rules
     *
     * @param $quote
     * @return int
     */
    public function getTotalPointsFirstPurchase($quote)
    {
        $totalPoints = 0;
        if ($this->validateFirstOrder($quote, 0)) {
            $rules = $this->ruleCollectionFactory->create()->addFieldToFilter('condition', 'firstpurchase');
            if (count($rules->getData())) {
                foreach ($rules as $rule) {
                    if (!$this->validateRule($rule)) {
                        continue;
                    }
                    $point = $this->validateMinSubtotal($rule, $quote);
                    if ($point) {
                        $totalPoints += $point;
                    }
                }
            }
        }

        return $totalPoints;
    }

    /**
     * Get default currency code
     *
     * @return mixed
     */
    public function getDefaultCurrencyCode()
    {
        return $this->_storeManager->getStore()->getDefaultCurrencyCode();
    }

    /**
     * Get default currency symbol
     *
     * @return string
     */
    public function getDefaultCurrencySymbol()
    {
        return $this->localeCurrency->getCurrency($this->getDefaultCurrencyCode())->getSymbol();
    }

    /**
     * Get points gained from lifetime amount rule
     *
     * @param $quote
     */
    public function getPointsLifetimeAmount($quote)
    {
        $ruleCollection = $this->ruleCollectionFactory->create()->addFieldToFilter('condition', 'lifetimeamount');
        if (!count($ruleCollection->getData())) {
            return;
        }

        $rule = $ruleCollection->getFirstItem();
        if (!$this->validateRule($rule)) {
            return;
        }
        $lifetimeConfig = $this->getLifetimeAmountConfig($rule);
        if ($lifetimeConfig === null) {
            return;
        }

        $amount = 0;

        switch ($lifetimeConfig['type']) {
            case self::INVOICED_AMOUNT:
                $amount = $this->getAccumInvoicedAmount($quote);
                $amount += $quote->getBaseSubtotal();
                break;
            case self::ORDERED_AMOUNT:
                $amount = $this->getAccumOrderedAmount($quote);
                $amount += $quote->getBaseSubtotal();
                break;
        }

        if (!$amount) {
            return;
        }

        if ($amount >= $lifetimeConfig['amount']) {
            return $this->updateAccumAmount($rule, $quote, $lifetimeConfig, $amount);
        }

        return;
    }

    /**
     * Get stacked ordered amount
     *
     * @param $quote
     */
    public function getAccumOrderedAmount($quote)
    {
        $lifetimeAmount = $this->lifetimeAmountFactory->create()->load($quote->getCustomerId(), 'customer_id');
        if (empty($lifetimeAmount->getData())) {
            return;
        }
        return $lifetimeAmount->getOrderedAmount();
    }

    /**
     * Get stacked invoiced amount
     *
     * @param $quote
     */
    public function getAccumInvoicedAmount($quote)
    {
        $lifetimeAmount = $this->lifetimeAmountFactory->create()->load($quote->getCustomerId(), 'customer_id');
        if (empty($lifetimeAmount->getData())) {
            return;
        }
        return $lifetimeAmount->getInvoicedAmount();
    }

    /**
     * Get additional rule configurations
     *
     * @param $rule
     * @return mixed|null
     */
    public function getAdditionalRuleConfigs($rule)
    {
        $ruleConfigs = $rule->getRuleConfigs();
        if (empty($ruleConfigs)) {
            return;
        }
        return Zend_Json::decode($ruleConfigs);
    }

    /**
     * Get lifetime amount configurations
     *
     * @param $rule
     * @return array|null
     */
    public function getLifetimeAmountConfig($rule)
    {
        $ruleConfigs = $this->getAdditionalRuleConfigs($rule);
        if ($ruleConfigs === null) {
            return;
        }
        return [
            'type' => $ruleConfigs['lt_amount_type'],
            'amount' => $ruleConfigs['lt_per_amount'],
        ];
    }

    /**
     * Validate lifetime amount
     * If valid, return gained points
     * If not valid, return null
     *
     * @param $rule
     * @param $quote
     */
    public function validateLifetimeAmount($rule, $quote)
    {
        $lifetimeConfig = $this->getLifetimeAmountConfig($rule);
        if ($lifetimeConfig === null) {
            return;
        }

        $amount = 0;

        switch ($lifetimeConfig['type']) {
            case self::INVOICED_AMOUNT:
                $amount = $this->getAccumInvoicedAmount($quote);
                break;
            case self::ORDERED_AMOUNT:
                $amount = $this->getAccumOrderedAmount($quote);
                break;
        }

        if (!$amount) {
            return;
        }

        if ($amount >= $lifetimeConfig['amount']) {
            return $this->updateAccumAmount($rule, $quote, $lifetimeConfig, $amount, 1);
        }

        return;
    }

    /**
     * @param $rule
     * @param $quote
     * @param $lifetimeConfig
     * @param $amount
     * @param int $active
     * @return float|int|void
     */
    public function updateAccumAmount($rule, $quote, $lifetimeConfig, $amount, $active = self::NOT_UPDATE)
    {
        switch ($lifetimeConfig['type']) {
            case self::INVOICED_AMOUNT:
                if ($lifetimeConfig['amount'] != 0) {
                    if ($this->getUpOrDown() == 'up') {
                        $count = ceil($amount / $lifetimeConfig['amount']);
                    } else {
                        $count = floor($amount / $lifetimeConfig['amount']);
                    }
                }
                if (isset($count) && $count > 0) {
                    if ($active) {
                        $lifetimeAmount = $this->lifetimeAmountFactory->create()->load($quote->getCustomerId(), 'customer_id');
                        if (!empty($lifetimeAmount->getData())) {
                            $remain = $amount - $lifetimeConfig['amount'] * $count;
                        }
                        $lifetimeAmount->setInvoicedAmount($remain)->save();
                    }
                    return $rule->getPoints() * $count;
                }
                break;

            case self::ORDERED_AMOUNT:
                if ($lifetimeConfig['amount'] != 0) {
                    if ($this->getUpOrDown() == 'up') {
                        $count = ceil($amount / $lifetimeConfig['amount']);
                    } else {
                        $count = floor($amount / $lifetimeConfig['amount']);
                    }
                }
                if (isset($count) && $count > 0) {
                    if ($active) {
                        $lifetimeAmount = $this->lifetimeAmountFactory->create()->load($quote->getCustomerId(), 'customer_id');
                        if (!empty($lifetimeAmount->getData())) {
                            $remain = $amount - $lifetimeConfig['amount'] * $count;
                        }
                        $lifetimeAmount->setOrderedAmount($remain)->save();
                    }
                    return $rule->getPoints() * $count;
                }
                break;
        }

        return;
    }

    /**
     * Earn point when doing payment
     * @param $order
     *
     * @throws LocalizedException
     */
    public function earnOrderPointsLifetimeAmount($order)
    {
        $quote = $this->quoteFactory->create()
            ->load($order->getQuoteId()); //to load orders created from backend correctly
        $orderId = $order->getId();
        $customerId = $order->getCustomerId();
        $rules = $this->ruleCollectionFactory->create()->addFieldToFilter('condition', 'lifetimeamount');

        // Return if earning points with discount isn't enabled
        if (!$this->canEarnPointsWithDiscount($quote)) {
            return;
        }

        if (!count($rules->getData())) {
            return;
        }
        $rule = $rules->getFirstItem();
        $point = 0;
        $ruleId = $rule->getId();
        $transaction = $this->findTransaction($customerId, $ruleId, $orderId, null);

        if ($this->validateRule($rule) && $transaction->getId() == null) {
            // add point from lifetime amount rule
            $point += $this->validateLifetimeAmount($rule, $quote);
            if (!$point) {
                return;
            }

            $isSubtract = $this->getSubtractPoint();
            if ($isSubtract) {
                if ((float)$quote->getSubtotalWithDiscount() === 0.00 || (float)$quote->getGrandTotal() === 0.00) {
                    $point = 0;
                }
            }
            $this->addAdditionalPointsForMembership($customerId, $point);

            // Update transaction history for reward points
            $this->updateTransactionHistory($order, $ruleId, $point);
        }
    }

    /**
     * @param $customerId
     * @param $originalPoints
     * @throws LocalizedException
     */
    public function addAdditionalPointsForMembership($customerId, $originalPoints)
    {
        if ($customerId) {
            $additionalPoints = $this->_membershipAction->setCustomerId($customerId)->applyAdditionalEarningPoints($originalPoints);
            if ($additionalPoints) {
                $model = $this->_transactionFactory->create();
                $model->setData([
                    'customer_id' => $customerId,
                    'rule_id' => -1,
                    'points_change' => $additionalPoints,
                    'comment' => 'Additional points for Membership'
                ]);
                $model->save();

                $pointAfter = $this->addPointsAccount($model->getCustomerId(), $model->getPointsChange(), $model->getId(), self::REWARD_MEMBERSHIP);

                $model->setData('points_after', $pointAfter);
                $model->save();
            }
        }
    }

    /**
     * Get lifetime amount rule
     *
     * @return DataObject|void
     */
    public function getLifetimeAmountRule()
    {
        $ruleCollection = $this->ruleCollectionFactory->create()->addFieldToFilter('condition', 'lifetimeamount');
        if (!count($ruleCollection->getData())) {
            return;
        }
        return $ruleCollection->getFirstItem();
    }

    /**
     * Check if earning points with applied points or discounted order is allowed
     *
     * @param $order
     * @return bool
     */
    public function canEarnPointsWithDiscountedOrder($order)
    {
        $quote = $this->quoteFactory->create()->load($order->getQuoteId());

        return $this->canEarnPointsWithDiscount($quote);
    }

    /**
     * Check if earning points with discount is enabled
     *
     * @param $quote
     * @return bool
     */
    public function canEarnPointsWithDiscount($quote)
    {
        if (!$this->getCanEarnPointWithAppliedPoints() && intval($quote->getData('reward_point')) > 0) {
            return false;
        }
        if (isset($quote)) {
            if (!$this->getCanEarnPointWithAppliedDiscount() && $quote->getCouponCode()) {
                return false;
            }
        } else {
            return null;
        }
        return true;
    }

    /**
     * Check if reward points notification message is enabled or not
     *
     * @return mixed
     */
    public function isLoginNoficationEnabled()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENABLE_REWARD_POINTS_LOGIN_NOFITICATION,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if customer placed order as guest
     *
     * @param $order
     * @return bool|mixed|null
     */
    public function isCheckoutAsGuest($order)
    {
        return $order->getCustomerIsGuest();
    }

    /**
     * Converts the amount value from one currency to another.
     * If the $currencyCodeFrom is not specified the current currency will be used.
     * If the $currencyCodeTo is not specified the base currency will be used.
     *
     * @param float $amountValue like 13.54
     * @param string|null $currencyCodeFrom like 'USD'
     * @param null $currencyCodeTo
     * @return string|null $currencyCodeTo like 'BYN'
     * @throws NoSuchEntityException
     */
    public function convert($amountValue, $currencyCodeFrom = null, $currencyCodeTo = null)
    {
        /**
         * If is not specified the currency code from which we want to convert - use current currency
         */
        if (!$currencyCodeFrom) {
            $currencyCodeFrom = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        }

        /**
         * If is not specified the currency code to which we want to convert - use base currency
         */
        if (!$currencyCodeTo) {
            $currencyCodeTo = $this->_storeManager->getStore()->getBaseCurrency()->getCode();
        }

        /**
         * Do not convert if currency is same
         */
        if ($currencyCodeFrom == $currencyCodeTo) {
            return $amountValue;
        }

        /** @var float $rate */
        // Get rate
        $rate = $this->currencyFactory->create()->load($currencyCodeFrom)->getAnyRate($currencyCodeTo);
        // Get amount in new currency
        $amountValue = $amountValue * $rate;

        return $amountValue;
    }

    /**
     * Get point range for bundle product
     *
     * @param $product
     * @return array
     * @throws LocalizedException
     */
    public function getBundleProductPointsRange($product)
    {
        $array = [];

        $typeInstance = $product->getTypeInstance();
        $optionIds = $typeInstance->getOptionsIds($product);
        if (empty($optionIds)) {
            return ['min' => 0, 'max' => 0];
        }
        foreach ($optionIds as $optionId) {
            $selections = $typeInstance->getSelectionsCollection($optionId, $product);
            foreach ($selections as $selection) {
                $productId = $selection->getEntityId();
                $array[$optionId][$productId]['point'] = $this->getProductPoints($productId);
                $array[$optionId][$productId]['qty'] = $selection->getSelectionQty() * 1;
            }
        }

        $options = $typeInstance->getOptionsCollection($product)->getItems();

        $min = $max = 0;
        foreach ($array as $key => $value) {
            $range = $this->getRangePoints($value);
            if ($options[$key]->getRequired()) {
                $min += $range['min'];
            }
            if ($options[$key]->getType() === 'checkbox' || $options[$key]->getType() === 'multi') {
                $max += $this->getMaxPointsOption($value);
            } else {
                 $max += $range['max'];
            }
        }

        return ['min' => $min, 'max' => $max];
    }

    /**
     * Get min points and max points of given ['point', 'qty'] array
     *
     * @param $array
     * @return array
     */
    public function getRangePoints($array)
    {
        $pointValues = [];
        foreach ($array as $value) {
            $pointValues[] = $value['point'] * $value['qty'];
        }

        return ['min' => min($pointValues), 'max' => max($pointValues)];
    }

    /**
     * Get max possible points from an option
     *
     * @param $array
     * @return float|int
     */
    public function getMaxPointsOption($array)
    {
        $sum = 0;
        foreach ($array as $key => $value) {
            $sum += $value['point'] * $value['qty'];
        }

        return $sum;
    }

    /**
     * Clear cache by type
     *
     * @param $types
     */
    public function clearCacheByType($types)
    {
        foreach ($types as $type) {
            $this->cacheTypeList->invalidate($type);
        }
    }

    /**
     * @param $order
     * @param $account
     * @param $point
     */
    public function getSendEmail($order, $account, $point, $rule_id, $ruleTitle, $customer)
    {
        try {
            if ($order != null) {
                $id = $order->getId();
                if ($id != null) {
                    $orderId = $id;
                } else {
                    $orderId = $order->getEntityId();
                }
                //Check the form to get points
                if ($ruleTitle != null) {
                    //getData with rules for adding points when registering, reviewing, or birthday.
                    $customerEmail = $order->getEmail();
                    $customerName = $order->getFirstname();
                    $incrementId = $ruleTitle;
                    $status = null;
                    $quoteId = null;
                } else {
                    $customerEmail = $order->getCustomerEmail();
                    $customerName = $order->getCustomerFirstname();
                    $incrementId = $order->getIncrementId();
                    $status = $order->getStatus();
                    $quoteId = $order->getQuoteId();
                }
            } else {
                $orderId = $customer->getId();
                $customerEmail = $customer->getEmail();
                $customerName = $customer->getFirstname();
                $incrementId = $ruleTitle;
                $status = null;
                $quoteId = null;
            }
            $this->_quote = $this->quoteFactory->create()->load($quoteId);
            if ($point == null) {
                $quotePoint = -$this->_quote->getData('reward_point') * 1;
            } else {
                $quotePoint = $point;
            }

            $currentPoint = $account->getData('points_current') + $quotePoint;
            $customerTier = $this->_membershipAction->setCustomerId($account->getCustomerId())->getTierByCustomerId();
            //Get the email template in config getBalanceTemplate
            $emailTemplateId = $this->getBalanceTemplate();
            $sender = $this->email->getSender();
            $recipients = [
                'name' => $customerName,
                'email' => $customerEmail
            ];
            $templateVars = [
                'name' => $recipients['name'],
                'order_id' => $orderId,
                'quote_point' => $quotePoint,
                'current_point' => $currentPoint,
                'current_tier' => ($customerTier && !empty($customerTier->getData(MembershipInterface::GROUP_NAME))) ? $customerTier->getData(MembershipInterface::GROUP_NAME) : __('Not rated'),
                'account_dashboard' => $this->_getUrl('customer/account/'),
                'increment_id' => $incrementId,
                'status' => $status,
                'comment' => $this->getPointComment($point, $rule_id)
            ];
            $this->email->sendCustomEmail($emailTemplateId, $templateVars, $sender, $recipients);
        } catch (Exception $exception) {
            $this->_logger->critical($exception->getMessage());
        }
    }

    protected function getPointComment($point, $ruleId)
    {
        $comment = '';
        if ($point == null) {
            $comment = self:: $messageminuspointswhenordering;
        } else {
            if ($ruleId == self::REWARD_POINT_FOR_ORDER) {
                $comment = self::$messagplusmoneywhenordering;
            } elseif ($ruleId == self::ADD_POINTS_BY_TRANSACTION) {
                $comment = self::$generalpointmessage;
            } elseif ($ruleId == self::EXPIRY_POINT) {
                $comment = self::$messagepointsexpired;
            } elseif ($ruleId == self::POINT_ADDED_BY_ADMIN) {
                $comment = self::$adminmessagesgivepoints;
            } elseif ($ruleId == self::POINT_BY_REFUNDED) {
                $comment = self::$messageRefundToGiftCard;
            } elseif ($ruleId == self::REWARD_REFERER) {
                $comment = self::$Referercode;
            } elseif ($ruleId == self::POINTS_VOIDED_AT_ORDER_REFUND) {
                $comment = self::$messageDeductPointRefund;
            } elseif ($ruleId == self::POINTS_RETURN_WHEN_REFUND) {
                $comment = self::$messageReturnPointRefund;
            } elseif ($ruleId == self::REWARD_MEMBERSHIP) {
                $comment = self::REWARD_MEMBERSHIP_MESSAGE;
            }
        }

        return $comment;
    }

    /**
     * @param $order
     * @param $customer
     * @param $sendBefore
     */
    public function getSendEmailExpired($userPoint, $customer, $sendBefore)
    {
        try {
            $orderId = $userPoint->getOrderId();
            $customerEmail = $customer->getEmail();
            $customerName = $customer->getFirstname();
            $status = $userPoint->getStatus();
            $transactionId = $userPoint->getTransactionId();
            $expiredDate = date('Y-m-d', strtotime($userPoint->getExpiredDate()));
            $pointsChange = $userPoint->getPointsChange();
            $insertionDate = date('Y-m-d', strtotime($userPoint->getInsertionDate()));

            $emailTemplateId = $this->getExpirationTemplate();
            $sender = $this->email->getSender();
            $recipients = [
                'name' => $customerName,
                'email' => $customerEmail
            ];
            $templateVars = [
                'name' => $recipients['name'],
                'order_id' => $orderId,
                'status' => $status,
                'transaction_id' => $transactionId,
                'expired_date' => $expiredDate,
                'points_change' => $pointsChange,
                'insertion_date' => $insertionDate,
                'send_before' => $sendBefore

            ];
            $this->email->sendCustomEmail($emailTemplateId, $templateVars, $sender, $recipients);
        } catch (Exception $exception) {
            $this->_logger->critical($exception->getMessage());
        }
    }

    //Merger module ReferAFriend

    /**
     * Check if module Magenest_RewardPoints is enabled
     * @return bool
     */
    public function isRewardPointsModuleEnabled()
    {
        if ($this->moduleManager->isEnabled('Magenest_RewardPoints')) {
            return true;
        }

        return false;
    }

    /**
     * Get code pattern
     * @return mixed
     */
    public function getCodePattern()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_REFERRAL_CODE_CODE_PATTERN,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if return by link feature is enabled in admin backend
     * @return mixed
     */
    public function isReferByLinkConfigEnabled()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_REFER_BY_LINK,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if return by link feature is enabled
     * @return mixed
     */
    public function isReferByLinkEnabled()
    {
        if ($this->isReferByLinkConfigEnabled() && $this->getEnableModule()) {
            return true;
        }

        return false;
    }

    /**
     * Get referral path used link referral link
     * @return mixed
     */
    public function getReferPath()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_REFER_PATH,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Specify referee or the referred or both will earn point
     * @return mixed
     */
    public function getReferralEarningType()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_REFERRAL_EARNING_TYPE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getReferralCode()
    {
        $customerId = $this->customerSession->getId();
        if (!$customerId) {
            return '';
        }

        $referralCode = $this->referralFactory->create()->load($customerId, 'customer_id');
        return $referralCode->getData('referral_code');
    }

    /**
     * @return mixed
     */
    public function couponAreAwardedTo()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_REFERRAL_COUPON_AWARDED_TO,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $path
     * @return mixed
     */
    public function whenReceivedCoupon($path)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getConfigValue($path)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $type
     * @param $recipients
     * @return |null
     */
    public function sendCoupon($type, $recipients)
    {
        $coupon = null;
        try {
            //get cart price rule
            if ($type == 'refered') {
                $cartPriceRuleId = $this->getConfigValue(self::XML_PATH_REFERRAL_SHOPPING_CART_RULE_REFERED);
                $emailTemplateId = $this->getConfigValue(self::XML_PATH_REFERRAL_EMAIL_TEMPLATE_REFERRED);
            }
            if ($type == 'referrer') {
                $cartPriceRuleId = $this->getConfigValue(self::XML_PATH_REFERRAL_SHOPPING_CART_RULE_REFERRER);
                $emailTemplateId = $this->getConfigValue(self::XML_PATH_REFERRAL_EMAIL_TEMPLATE_REFERRER);
            }
            $sender = $this->email->getSender();
            $massgenerator = $this->massgeneratorFactory->create();
            $massgenerator->setFormat('alphanum');
            $massgenerator->setLength(10);
            $codes = $massgenerator->generateCouponPool($cartPriceRuleId, 1);
            $coupon = $codes[0];
            $templateVars = [
                'name' => $recipients['name'],
                'coupon' => $coupon
            ];
            $this->email->sendCustomEmail($emailTemplateId, $templateVars, $sender, $recipients);
        } catch (Exception $exception) {
            $this->_logger->critical($exception->getMessage());
        }
        return $coupon;
    }

    /**
     * @param $recipients
     */
    public function sendCouponForAffiliateAndBirthday(
        $recipients,
        $pathApply,
        $pathCartRule,
        $pathTemplate
    ) {
        try {
            //get cart price rule
            $isApply = $this->getConfigValue($pathApply);
            if ($isApply) {
                $cartPriceRuleId = $this->getConfigValue($pathCartRule);
                $emailTemplateId = $this->getConfigValue($pathTemplate);
                if ($cartPriceRuleId && $emailTemplateId) {
                    $sender = $this->email->getSender();
                    $massgenerator = $this->massgeneratorFactory->create();
                    $massgenerator->setFormat('alphanum');
                    $massgenerator->setLength(10);
                    $codes = $massgenerator->generateCouponPool($cartPriceRuleId, 1);
                    $coupon = $codes[0];
                    $templateVars = [
                        'name' => $recipients['name'],
                        'coupon' => $coupon
                    ];
                    $this->email->sendCustomEmail($emailTemplateId, $templateVars, $sender, $recipients);
                }
            }

        } catch (Exception $exception) {
            $this->_logger->critical($exception->getMessage());
        }
    }

    /**
     * @param $order
     * @param $quote
     * @param $subtractPercent
     * @param $ruleId
     * @return float|int
     * @throws LocalizedException
     */
    public function getQuoteMultiShipping($order, $quote, $subtractPercent, $ruleId)
    {
        $point = 0;
        $data = $order->getData();
        $items = $data['items'];
        foreach ($items as $orderItem) {
            $item = $quote->getItemById($orderItem->getQuoteItemId());
            $rowTotal = $item->getRowTotal();
            $price = $item->getPrice() * $item->getQty();
            if ($rowTotal != 0 && $price != 0) {
                $total = $rowTotal / $price;
            } else {
                $total = self::UPDATE;
            }
            $point = $this->checkPoint($item, $rowTotal, $subtractPercent, $total, $point, $ruleId);
        }
        return $point;
    }

    /**
     * @param $item
     * @param $rowTotal
     * @param $subtractPercent
     * @param $total
     * @param $totalPoints
     * @param null $ruleId
     * @return float
     * @throws LocalizedException
     */
    public function checkPoint($item, $rowTotal, $subtractPercent, $total, $totalPoints, $ruleId = null)
    {
        if ($this->getMagentoVersion() >= '2.3.4' && $item->getParentItemId() != null && $rowTotal == 0) {
            $productPoints = $this->getQuoteItemPoints($item, $subtractPercent, $ruleId) * $item->getQty();
            $totalPoints += round($productPoints, 2);
        } else {
            $productPoints = $this->getQuoteItemPoints($item, $subtractPercent, $ruleId) * $item->getQty() * $total;
            $totalPoints += round($productPoints, 2);
        }
        return $totalPoints;
    }

    /**
     * @param int $value
     * @return false|float|int
     */
    public function convertValueToPoint($value = 0)
    {
        $point = 0;
        if ($value) {
            $convertRatio = $this->getRewardBaseAmount();
            if ($this->getUpOrDown() == self::POINT_ROUNDING_UP) {
                $point = ceil($value * $convertRatio);
            } else {
                $point = floor($value * $convertRatio);
            }
        }

        return $point;
    }

    /**
     * @return bool
     */
    public function isShowPointForGuest()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_CONFIG_SHOW_POINT_FOR_GUEST, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isEnableConvertKpoint()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLE_CONVERT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getRateConvertKpoint()
    {
        if ($this->isEnableConvertKpoint()) {
            return $this->scopeConfig->getValue(self::XML_PATH_RATE_CONVERT, ScopeInterface::SCOPE_STORE);
        }
        return null;
    }
}
