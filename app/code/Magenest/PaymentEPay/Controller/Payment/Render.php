<?php

namespace Magenest\PaymentEPay\Controller\Payment;

use Magenest\PaymentEPay\Helper\Data;
use Magento\Checkout\Model\Session;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Vault\Api\PaymentTokenManagementInterface;
use Magento\Vault\Model\ResourceModel\PaymentToken as PaymentTokenResourceModel;
use Magenest\PaymentEPay\Api\Data\PaymentAttributeInterface;
/**
 * Class Response
 * @package Magenest\OnePay\Controller\Order
 */
class Render extends \Magento\Framework\App\Action\Action
{

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var Data
     */
    protected $helperData;


    protected $_pageFactory;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var PaymentTokenResourceModel
     */
    protected $paymentTokenResourceModel;

    /**
     * PaymentTokenManagementInterface $paymentTokenManagement
     */
    private $paymentTokenManagement;

    private $serializer;

    protected $customerSession;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        Data $helperData,
        Session $checkoutSession,
        OrderFactory $orderFactory,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        PaymentTokenManagementInterface $paymentTokenManagement,
        PaymentTokenResourceModel $paymentTokenResourceModel,
        \Magento\Customer\Model\Session $customerSession,
        SerializerInterface $serializer
    )
    {
        $this->helperData = $helperData;
        $this->_pageFactory = $pageFactory;
        $this->checkoutSession = $checkoutSession;
        $this->orderFactory    = $orderFactory;
        $this->paymentTokenManagement = $paymentTokenManagement;
        $this->paymentTokenResourceModel = $paymentTokenResourceModel;
        $this->serializer = $serializer;
        $this->customerSession = $customerSession;
        return parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $customerId = $this->customerSession->getCustomer()->getId();
        $payToken = [];
        $paymentTokenList = $this->paymentTokenManagement->getListByCustomerId($customerId);
        if(!empty($paymentTokenList)) {
            foreach ($paymentTokenList as $paymentToken) {
                if($paymentToken->getData('payment_method_code') == PaymentAttributeInterface::CODE_VNPT_EPAY &&
                    $paymentToken->getData('is_visible') == PaymentAttributeInterface::IS_VISIBLE_TOKEN &&
                    $paymentToken->getData('is_active')  == PaymentAttributeInterface::IS_ACTIVE_TOKEN
                ){
                    $payToken[] = $this->serializer->unserialize($paymentToken->getData('details'))['type'];
                }
            }
        }
        $result = $payToken;
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($result);
        return $resultJson;
    }
}
