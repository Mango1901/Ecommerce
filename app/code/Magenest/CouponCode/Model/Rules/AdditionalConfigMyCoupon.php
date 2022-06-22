<?php

namespace Magenest\CouponCode\Model\Rules;

use Magenest\CouponCode\Helper\Data;
use Magenest\CouponCode\Model\ResourceModel\ClaimCoupon\CollectionFactory;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\View\Asset\Repository;
use Magento\SalesRule\Model\RuleFactory;
use Magento\SalesRule\Model\RuleRepository;

class AdditionalConfigMyCoupon implements ConfigProviderInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var Data
     */
    protected $_dataHelper;

    /**
     * @var Repository
     */
    protected $asset;

    /**
     * @var RuleRepository
     */
    protected $ruleRepository;

    /**
     * @var RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * Constructor
     *
     * @param CollectionFactory $collectionFactory
     * @param UserContextInterface $userContext
     * @param Data $dataHelper
     * @param Repository $asset
     * @param RuleRepository $ruleRepository
     * @param RuleFactory $ruleFactory
     * @param Session $checkoutSession
     */
    public function __construct(
        CollectionFactory    $collectionFactory,
        UserContextInterface $userContext,
        Data $dataHelper,
        Repository $asset,
        RuleRepository $ruleRepository,
        RuleFactory $ruleFactory,
        Session $checkoutSession
    ) {
        $this->userContext = $userContext;
        $this->collectionFactory = $collectionFactory;
        $this->_dataHelper = $dataHelper;
        $this->asset = $asset;
        $this->ruleRepository = $ruleRepository;
        $this->ruleFactory = $ruleFactory;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Additional config
     *
     * @return array|null
     */
    public function getConfig()
    {
        $userId = $this->userContext->getUserId();
        if ($userId != null) {
            $enableCouponListing = $this->_dataHelper->getEnableCouponListingConfiguration();
            $additionalData = $this->collectionFactory->create()
                ->addFieldToFilter('customer_id', $userId)
                ->setOrder('claimed_at', 'DESC')
                ->getData();

            $couponCodeUsed = $this->checkoutSession->getQuote()->getCouponCode();
            $isApplied = false;
            foreach ($additionalData as &$datum) {
                if ($couponCodeUsed === $datum['code']) {
                    $datum['using'] = true;
                    $isApplied = true;
                } else {
                    $datum['using'] = false;
                }
            }

            $additionalVariables = [
                'customer_coupon' => $additionalData,
                'enable' => $enableCouponListing,
                'is_applied' => $isApplied,
                'default_image' => $this->asset->getUrl('Magenest_CouponCode::image/default-coupon.png')
            ];
            return $additionalVariables;
        }
        return [];
    }
}
