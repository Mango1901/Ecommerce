<?php
namespace Magenest\PhotoReview\Model\Coupon;

class Massgenerator extends \Magento\SalesRule\Model\Coupon\Massgenerator
{
    /** @var \Magento\SalesRule\Model\RuleFactory  */
    protected $_salesRuleFactory;

    /** @var \Magento\SalesRule\Model\ResourceModel\Rule  */
    protected $_salesRuleResource;

    /** @var \Magento\SalesRule\Model\ResourceModel\Coupon  */
    protected $_couponResource;

    /** @var \Psr\Log\LoggerInterface  */
    protected $_logger;

    /**
     * Massgenerator constructor.
     *
     * @param \Magento\SalesRule\Model\RuleFactory $salesRuleFactory
     * @param \Magento\SalesRule\Model\ResourceModel\Rule $salesRuleResource
     * @param \Magento\SalesRule\Model\ResourceModel\Coupon $couponResource
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\SalesRule\Helper\Coupon $salesRuleCoupon
     * @param \Magento\SalesRule\Model\CouponFactory $couponFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\SalesRule\Model\RuleFactory $salesRuleFactory,
        \Magento\SalesRule\Model\ResourceModel\Rule $salesRuleResource,
        \Magento\SalesRule\Model\ResourceModel\Coupon $couponResource,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\SalesRule\Helper\Coupon $salesRuleCoupon,
        \Magento\SalesRule\Model\CouponFactory $couponFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ){
        $this->_salesRuleFactory = $salesRuleFactory;
        $this->_salesRuleResource = $salesRuleResource;
        $this->_couponResource = $couponResource;
        $this->_logger = $logger;
        parent::__construct($context, $registry, $salesRuleCoupon, $couponFactory, $date, $dateTime, $resource, $resourceCollection, $data);
    }

    public function generateCoupon($ruleId)
    {
        try{
            /** @var \Magento\SalesRule\Model\Rule $ruleModel */
            $ruleModel = $this->_salesRuleFactory->create();
            $this->_salesRuleResource->load($ruleModel,$ruleId);
            if($ruleModel->getRuleId()){
                $maxAttempts          = $this->getMaxAttempts() ? $this->getMaxAttempts() : self::MAX_GENERATE_ATTEMPTS;
                $this->increaseLength();
                /** @var $coupon \Magento\SalesRule\Model\Coupon */
                $coupon       = $this->couponFactory->create();
                $nowTimestamp = $this->dateTime->formatDate($this->date->gmtTimestamp());
                $attempt = 0;
                do {
                    if ($attempt >= $maxAttempts) {
                        throw new \Magento\Framework\Exception\LocalizedException(
                            __('We cannot create the requested Coupon Qty. Please check your settings and try again.')
                        );
                    }
                    $code = $this->generateCode();
                    ++$attempt;
                } while ($this->_couponResource->exists($code));
                $expirationDate = $ruleModel->getToDate();
                if ($expirationDate instanceof \DateTimeInterface) {
                    $expirationDate = $expirationDate->format('Y-m-d H:i:s');
                }
                $coupon->setRuleId($ruleId)
                    ->setUsageLimit($ruleModel->getUsesPerCoupon())
                    ->setUsagePerCustomer($ruleModel->getUsagePerCustomer())
                    ->setExpirationDate($expirationDate)
                    ->setCreatedAt($nowTimestamp)
                    ->setType(\Magento\SalesRule\Helper\Coupon::COUPON_TYPE_SPECIFIC_AUTOGENERATED)
                    ->setCode($code);
                $this->_couponResource->save($coupon);
                return $code;
            }
        }catch (\Exception $exception){
            $this->_logger->critical($exception->getMessage());
            return null;
        }
    }
}