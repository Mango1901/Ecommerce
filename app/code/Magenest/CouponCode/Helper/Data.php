<?php
namespace Magenest\CouponCode\Helper;

use Magenest\CouponCode\Model\Configurations\Active;
use Magenest\CouponCode\Model\Configurations\CustomerGroup;
use Magenest\CouponCode\Model\Configurations\FromDate;
use Magenest\CouponCode\Model\Configurations\ToDate;
use Magenest\CouponCode\Model\Configurations\UsesPerCoupon;
use Magenest\CouponCode\Model\Configurations\UsesPerCustomer;
use Magenest\CouponCode\Model\Configurations\Website;
use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public const COMMUNITY = 'Community';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $_productMetadata;

    /**
     * @var string
     */
    public const XML_PATH_WEBSITE = 'magenest_coupon/couponcode/website_id';
    public const XML_PATH_CUSTOMER_GROUP = 'magenest_coupon/couponcode/customer_group';
    public const XML_PATH_ACTIVE = 'magenest_coupon/couponcode/is_active';
    public const XML_PATH_FROM = 'magenest_coupon/couponcode/from_date';
    public const XML_PATH_TO = 'magenest_coupon/couponcode/to_date';
    public const XML_PATH_USES_PER_COUPON = 'magenest_coupon/couponcode/usage_limit';
    public const XML_PATH_USES_PER_CUSTOMER = 'magenest_coupon/couponcode/usage_per_customer';
    public const XML_PATH_ENABLE = 'magenest_coupon/couponlisting/enable';
    public const XML_PATH_SHOW_FIELDS = 'magenest_coupon/couponlisting/showField';

    /**
     * @var array
     */
    protected $_configurations =[
        Active::CODE => self::XML_PATH_ACTIVE,
        Website::CODE => self::XML_PATH_WEBSITE,
        CustomerGroup::CODE => self::XML_PATH_CUSTOMER_GROUP,
        FromDate::CODE => self::XML_PATH_FROM,
        ToDate::CODE => self::XML_PATH_TO,
        UsesPerCoupon::CODE => self::XML_PATH_USES_PER_COUPON,
        UsesPerCustomer::CODE => self::XML_PATH_USES_PER_CUSTOMER
    ];

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_productMetadata = $productMetadata;

        parent::__construct($context);
    }

    /**
     * Get Current website id
     *
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrentWebsiteId()
    {
        return (int) $this->_storeManager->getStore()->getWebsiteId();
    }

    /**
     * Check community edition
     *
     * @return bool
     */
    public function checkCommunityEdition()
    {
        return $this->_productMetadata->getEdition() == self::COMMUNITY;
    }

    /**
     * Get magento version
     *
     * @return string
     */
    public function getMagentoVersion()
    {
        return $this->_productMetadata->getVersion();
    }

    /**
     * Get configuration field by code
     *
     * @param string $code
     * @return bool
     */
    public function getConfigurationFieldByCode(string $code)
    {
        return $this->scopeConfig->isSetFlag($this->_configurations[$code], ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get enable coupon listing config
     *
     * @return bool
     */
    public function getEnableCouponListingConfiguration()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get field display listing configuration
     *
     * @return mixed
     */
    public function getFieldDisplayListingConfiguration()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SHOW_FIELDS, ScopeInterface::SCOPE_STORE);
    }
}
