<?php
namespace Magenest\CouponCode\ViewModel;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Http\Context;
use \Magento\Framework\App\Config\ScopeConfigInterface;

class Coupon implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    public const ENABLE_LISTING = 'magenest_coupon/couponlisting/enable';
    /**
     * @var ScopeConfigInterface
     */
    private $scope;
    /**
     * @var Context
     */
    private $httpContext;
    /**
     * @var ProductMetadataInterface
     */
    private $_productMetadata;
    /**
     * @var Json
     */
    private $json;
    /**
     * @var DateTime
     */
    private $date;

    /**
     * Constructor
     *
     * @param ScopeConfigInterface $scope
     * @param Context $httpContext
     * @param ProductMetadataInterface $productMetadata
     * @param Json $json
     * @param DateTime $date
     */
    public function __construct(
        ScopeConfigInterface $scope,
        Context $httpContext,
        ProductMetadataInterface $productMetadata,
        Json $json,
        DateTime $date
    ) {
        $this->scope = $scope;
        $this->httpContext = $httpContext;
        $this->_productMetadata = $productMetadata;
        $this->json = $json;
        $this->date = $date;
    }
    /**
     * Get current user id
     *
     * @return int|null
     */
    public function getUserId()
    {
        return $this->httpContext->getValue('customer_id');
    }
    /**
     * Get current customer group
     *
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomerGroup()
    {
        return $this->httpContext->getValue('customer_group_id');
    }
    /**
     * Today
     *
     * @return false|int
     */
    public function getToday()
    {
        return $this->date->gmtTimestamp();
    }
    /**
     * Unserialize Data
     *
     * @param string $data
     * @return array|bool|float|int|mixed|string|null
     */
    public function handleImage($data)
    {
        $img = $this->json->unserialize($data);
        if (isset($img[0]['url'])) {
            return $img[0]['url'];
        }
        return null;
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
     * Get enable listing
     *
     * @return bool
     */
    public function enableListing()
    {
        return $this->scope->isSetFlag(self::ENABLE_LISTING);
    }

    /**
     * Handle description
     *
     * @param string $description
     * @return array
     */
    public function handleDescription($description){
        $contentDisplay = substr($description, 0, 120);
        $fixedDescriptions = strpos($description, '"')
            ? str_replace('"',"'",$description) : $description;
        $fixedDescriptions = trim(preg_replace('/\s\s+/', ' ', $fixedDescriptions));
        return [
            'display' => $contentDisplay,
            'fixed' => $fixedDescriptions
        ];
    }
}
