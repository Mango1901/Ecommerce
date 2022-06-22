<?php
namespace Magenest\PhotoReview\Model;

use Magenest\PhotoReview\Helper\Data;
use Magenest\PhotoReview\Model\ReminderEmail;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\DB\Select;

class SendCouponCron
{
    /** @var \Magenest\PhotoReview\Model\ResourceModel\ReminderEmail  */
    protected $_reminderEmailResource;

    /** @var \Magenest\PhotoReview\Model\ResourceModel\ReminderEmail\CollectionFactory  */
    protected $_reminderEmailCollection;

    /** @var \Magenest\PhotoReview\Helper\Data  */
    protected $_helperData;

    /** @var \Magenest\PhotoReview\Model\Coupon\Massgenerator  */
    protected $_generateCoupon;

    /** @var \Magenest\PhotoReview\Helper\Email  */
    protected $_sendEmailHelper;

    /** @var \Psr\Log\LoggerInterface  */
    protected $_logger;

    /** @var \Magento\Customer\Api\CustomerRepositoryInterface  */
    protected $customerRepository;

    /** @var \Magento\Review\Model\ResourceModel\Review\CollectionFactory $_reviewCollection */
    protected $_reviewCollection;

    /** @var \Mageneto\Review\Model\ReviewFactory  */
    protected $_reviewFactory;

    /** @var \Magento\Review\Model\ResourceModel\Review  */
    protected $_reviewResource;

    /** @var \Magento\Framework\Stdlib\DateTime\DateTime $_dateTime */
    protected $_dateTime;

    /** @var \Magento\Sales\Api\OrderRepositoryInterface  */
    protected $orderRepository;

    /** @var \Magento\Framework\Api\SearchCriteriaBuilder */
    protected $searchCriteriaBuilder;

    protected $customerModel = null;

    /**
     * SendCouponCron constructor.
     *
     * @param ResourceModel\ReminderEmail $reminderEmailResource
     * @param ResourceModel\ReminderEmail\CollectionFactory $reminderEmailCollection
     * @param Data $helperData
     * @param Coupon\Massgenerator $generateCoupon
     * @param \Magenest\PhotoReview\Helper\Email $sendEmailHelper
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollection
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param \Magento\Review\Model\ResourceModel\Review $reviewResource
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $_dateTime
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteria
     */
    public function __construct(
        \Magenest\PhotoReview\Model\ResourceModel\ReminderEmail $reminderEmailResource,
        \Magenest\PhotoReview\Model\ResourceModel\ReminderEmail\CollectionFactory $reminderEmailCollection,
        \Magenest\PhotoReview\Helper\Data $helperData,
        \Magenest\PhotoReview\Model\Coupon\Massgenerator $generateCoupon,
        \Magenest\PhotoReview\Helper\Email $sendEmailHelper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollection,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Review\Model\ResourceModel\Review $reviewResource,
        \Magento\Framework\Stdlib\DateTime\DateTime $_dateTime,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteria
    ){
        $this->_reminderEmailResource = $reminderEmailResource;
        $this->_reminderEmailCollection = $reminderEmailCollection;
        $this->_helperData = $helperData;
        $this->_generateCoupon = $generateCoupon;
        $this->_sendEmailHelper = $sendEmailHelper;
        $this->_logger = $logger;
        $this->customerRepository = $customerRepository;
        $this->_reviewCollection = $reviewCollection;
        $this->_reviewFactory = $reviewFactory;
        $this->_reviewResource = $reviewResource;
        $this->_dateTime = $_dateTime;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteria;
    }
    public function sendCoupon($reviewModel,$productId,$customerId)
    {
        $isEnableCouponForReview = (boolean)$this->_helperData->getScopeConfig(Data::ENABLE_COUPON_FOR_REVIEW);
        $customer = $this->getCustomerById($customerId);
        if($isEnableCouponForReview){
            if($reviewModel->getReviewId()){
                $this->generateEmail($customer,$productId);
            }
        }
    }
    public function generateEmail($customer, $productId)
    {
        $status = false;
        try{
            $customerName = $customer->getFirstname()." ".$customer->getLastname();
            $customerEmail = $customer->getEmail();
            $ruleId = (int)$this->_helperData->getScopeConfig(Data::CART_PRICE_RULE);

            if($ruleId != ""){
                $this->_generateCoupon->setFormat('alphanum');
                $this->_generateCoupon->setLength(10);
                $couponCode = $this->_generateCoupon->generateCoupon($ruleId);
                $emailSender = $this->_helperData->getScopeConfig(Data::COUPON_EMAIL_SENDER);
                $emailTemplate = $this->_helperData->getScopeConfig(Data::COUPON_EMAIL_TEMPLATE);
                $emailVars = [
                    'customer_name' => $customerName,
                    'coupon_code' => $couponCode
                ];
                $recipient = [
                    'email' => $customerEmail,
                    'name' => $customerName
                ];
                if($couponCode != null){
                    $status = $this->_sendEmailHelper->sendEmail($emailTemplate,$emailVars,$emailSender,$recipient);
                }
            }
        }catch (\Exception $exception){
            $this->_logger->critical($exception->getMessage());
        }
        return $status;
    }
    public function cancelReminderEmail($customerEmail, $productId)
    {
        try{
            $collection = $this->_reminderEmailCollection->create()
                ->addFieldToFilter('email', $customerEmail)
                ->addFieldToFilter('status', ReminderEmail::STATUS_QUEUE)
                ->getSelect()
                ->reset(Select::COLUMNS)
                ->columns([
                    'order_id'
                ]);
            $orderIds = $this->_reminderEmailResource->getConnection()->fetchCol($collection);
            if(count($orderIds) > 0){
                $searchCriteria = $this->searchCriteriaBuilder
                    ->addFilter(OrderInterface::ENTITY_ID, $orderIds,'in')
                    ->create();
                $orders = $this->orderRepository->getList($searchCriteria)->getItems();
                $ids = [];
                /** @var \Magento\Sales\Api\Data\OrderInterface $order */
                foreach ($orders as $order){
                    $items = $order->getItems();
                    /** @var \Magento\Sales\Api\Data\OrderItemInterface $item */
                    foreach ($items as $item){
                        if($item->getProductId() == $productId){
                            $ids[] = $order->getEntityId();
                        }
                    }
                }
                if(!empty($ids)){
                    $this->_reminderEmailResource->updateMultiStatus($ids,ReminderEmail::STATUS_CANCEL,'order_id');
                }
            }
        }catch (\Exception $exception){
            $this->_logger->critical($exception->getMessage());
        }
    }

    public function getCustomerById($customerId)
    {
        if($this->customerModel == null){
            $this->customerModel = $this->customerRepository->getById($customerId);
        }
        return $this->customerModel;
    }
}