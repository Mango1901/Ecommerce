<?php

namespace Magenest\PaymentEPay\Controller\Payment;

use Magenest\PaymentEPay\Helper\Data;
use Magento\Checkout\Model\Session;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Sales\Model\OrderFactory;
/**
 * Class Response
 * @package Magenest\OnePay\Controller\Order
 */
class ReturnPayment extends \Magento\Framework\App\Action\Action
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

    private $serializer;

    protected $customerSession;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        Data $helperData,
        Session $checkoutSession,
        OrderFactory $orderFactory,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Customer\Model\Session $customerSession,
        SerializerInterface $serializer
    )
    {
        $this->helperData = $helperData;
        $this->_pageFactory = $pageFactory;
        $this->checkoutSession = $checkoutSession;
        $this->orderFactory    = $orderFactory;
        $this->serializer = $serializer;
        $this->customerSession = $customerSession;
        return parent::__construct($context);
    }

    /**
     *
     */
    public function execute()
    {
        $orderCurrent = $this->checkoutSession->getLastRealOrder();
        $orderIncrementId = $orderCurrent->getIncrementId();
        $order = $this->orderFactory->create()->loadByIncrementId($orderIncrementId);
        if($order->getStatus() == 'pending_payment') {
            $result = array('orderStatus' => $order->getStatus() , 'url' => 'onepage/success');
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($result);
            return $resultJson;
        }
    }
}
