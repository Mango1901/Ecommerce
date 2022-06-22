<?php

namespace Magenest\PhotoReview\Helper;

use Magenest\PhotoReview\Block\Adminhtml\Form\Field\SummaryOptions;
use Magenest\PhotoReview\Model\ResourceModel\ReviewDetail;
use Magenest\PhotoReview\Model\ResourceModel\ReviewDetail\CollectionFactory;
use Magenest\PhotoReview\Model\ReviewDetailFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    const ENABLE_MODULE = 'photoreview/general/enable';
    const ENABLE_PROSCONS = 'photoreview/general/pros_cons';
    const ENABLE_ADMIN_COMMENT = 'photoreview/general/allow_admin_comment';
    const ENABLE_ADD_EXTERRAL_LINKS = 'photoreview/general/external_links';
    const MAX_PHOTO_UPLOAD = 'photoreview/general/max_photo_upload';
    const REQUIRED_PHOTO = 'photoreview/general/photo_required';
    const SUMMARY_OPTIONS = 'photoreview/general/summary_options';
    const ENABLE_AUTO_APPROVED_REVIEW = 'photoreview/general/auto_approved_review';
    const ENABLE_EMAIL_REMINDER = 'photoreview/review_reminder/enable';
    const SEND_AFTER = 'photoreview/review_reminder/send_after';
    const REMINDER_EMAIL_TEMPLATE = 'photoreview/review_reminder/email_template';
    const REMINDER_EMAIL_SENDER = 'photoreview/review_reminder/email_sender';
    const ENABLE_COUPON_FOR_REVIEW = 'photoreview/coupon_review/enable';
    const CONDITION_SEND_COUPON = 'photoreview/coupon_review/send_coupon_for';
    const CART_PRICE_RULE = 'photoreview/coupon_review/apply_cartrule';
    const COUPON_EMAIL_SENDER = 'photoreview/coupon_review/email_sender';
    const COUPON_EMAIL_TEMPLATE = 'photoreview/coupon_review/email_template';
    const ADD_LINK_TOPMENU = 'photoreview/general/add_link_to_frontend';
    const MENU_TITLE = 'photoreview/general/menu_title';
    const APP_ID = 'photoreview/general/facebook_app_ai';
    const MAX_VIDEO_UPLOAD = 'photoreview/general/max_video_upload';
    const REQUIRED_VIDEO = 'photoreview/general/video_required';
    const MAX_VIDEO_SIZE_UPLOAD = 'photoreview/general/max_video_size_upload';

    /** @var StoreManagerInterface */
    protected $_storeManager;

    /** @var ReviewDetailFactory */
    protected $_customReviewFactory;

    /** @var ReviewDetail */
    protected $_customReviewResource;

    /** @var CollectionFactory */
    protected $_customReviewCollection;

    protected $scope = null;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * Data constructor.
     *
     * @param ReviewDetailFactory $customReviewFactory
     * @param ReviewDetail $customReviewResource
     * @param CollectionFactory $collectionFactory
     * @param StoreManagerInterface $storeManager
     * @param Json $serializer
     * @param Context $context
     */
    public function __construct(
        ReviewDetailFactory   $customReviewFactory,
        ReviewDetail          $customReviewResource,
        CollectionFactory     $collectionFactory,
        StoreManagerInterface $storeManager,
        Json $serializer,
        Context               $context
    ) {
        $this->_customReviewFactory = $customReviewFactory;
        $this->_customReviewResource = $customReviewResource;
        $this->_customReviewCollection = $collectionFactory;
        $this->_storeManager = $storeManager;
        $this->serializer = $serializer;
        $storeId = $this->_storeManager->getStore()->getId();
        $this->scope = ($storeId === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_STORES);
        parent::__construct($context);
    }

    /**
     * Get review statuses as option array
     *
     * @return array
     */
    public function getIsRecommendOptionArray()
    {
        return $result = [
            ['value' => 0, 'label' => __('No')],
            ['value' => 1, 'label' => __('Yes')]
        ];
    }
    public function getScope(){
        if($this->scope == null){
            $storeId = $this->_storeManager->getStore()->getId();
            $this->scope = ($storeId === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_STORES);
        }
        return $this->scope;
    }
    public function getBaseReviewUrl()
    {
        $url = 'photoreview/gallery';
        return $this->_storeManager->getStore()->getBaseUrl() . $url;
    }

    public function isModuleEnable()
    {
        return $this->scopeConfig->isSetFlag(self::ENABLE_MODULE, $this->scope);
    }

    public function isEnableProsCons()
    {
        return $this->scopeConfig->isSetFlag(self::ENABLE_PROSCONS, $this->scope);
    }

    public function isActiveMenu()
    {
        return $this->scopeConfig->isSetFlag(self::ADD_LINK_TOPMENU, $this->scope);
    }

    public function isEnableReminder()
    {
        $scope = $this->getScope();
        return $this->scopeConfig->isSetFlag(self::ENABLE_EMAIL_REMINDER, $scope);
    }

    public function getScopeConfig($path)
    {
        $result = $this->scopeConfig->getValue($path, $this->scope);
        return $result;
    }

    public function getCustomReviewDetail($reviewId)
    {
        $reviewDetail = $this->_customReviewCollection->create()
            ->addFieldToFilter('review_id', $reviewId)
            ->getFirstItem()
            ->getData();
        return $reviewDetail;
    }

    /**
     * @return bool
     */
    public function isEnableAutoApproved()
    {
        $scope = $this->getScope();
        return $this->scopeConfig->isSetFlag(self::ENABLE_AUTO_APPROVED_REVIEW, $scope);
    }

    /**
     * @return array
     */
    public function getLabelSummary()
    {
        $result = [];
        $summaryOptions = $this->getScopeConfig(Data::SUMMARY_OPTIONS);
        try {
            $optionArray = $this->serializer->unserialize($summaryOptions);
            foreach ($optionArray as $option) {
                $result[] = [
                    'label' => $option[SummaryOptions::SUMMARY_LABEL],
                    'value' => $option[SummaryOptions::SUMMARY_CODE]
                ];
            }
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
        return $result;
    }
}
