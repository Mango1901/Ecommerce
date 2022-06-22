<?php

namespace Magenest\PaymentEPay\Controller\Payment;

use Magenest\PaymentEPay\Api\Data\HandlePaymentInterface;
use Magenest\PaymentEPay\Api\Data\PaymentAttributeInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

class RePayment extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Session
     */
    protected $checkoutSession;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;

    /**
     * @var HandlePaymentInterface
     */
    private $handlePaymentInterface;
    /**
     * @var \Magenest\PaymentEPay\Logger\Logger
     */
    protected $_logger;

    protected $orderRepository;

    public function __construct(
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Framework\App\Action\Context $context,
        Session $checkoutSession,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magenest\PaymentEPay\Logger\Logger $logger,
        HandlePaymentInterface $handlePaymentInterface
    ) {
        $this->orderRepository = $orderRepository;
        $this->_pageFactory = $pageFactory;
        $this->checkoutSession = $checkoutSession;
        $this->handlePaymentInterface = $handlePaymentInterface;
        $this->_logger = $logger;
        return parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        try {
            $params = $this->getRequest()->getParams();
            $orderId = $params["orderId"];
            $order = $this->orderRepository->get($orderId);
            $orderIncrementId = $order->getIncrementId();
            $payOption = !empty($params['payOption']) ? $params['payOption'] : '';
            $payType = !empty($params['payType']) ? $params['payType'] : '';
            if ($payOption == PaymentAttributeInterface::PAY_CREATE_TOKEN
                || $payOption == PaymentAttributeInterface::PAY_RETURNED_TOKEN
                || $payOption == '') {
                if ($payType == 'VA') {
                    $result = $this->handlePaymentInterface->handleQRCodePayment($orderIncrementId, $payOption);
                    $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
                    $resultJson->setData($result);
                    $this->_logger->info('-----------Request Data' . $orderIncrementId . ':' . json_encode($result));
                } elseif ($payType == 'IS') {
                    $result = $this->handlePaymentInterface->handleInstallmentPayment($orderIncrementId, $payOption);
                    $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
                    $resultJson->setData($result);
                    $this->_logger->info('-----------Request Data' . $orderIncrementId . ':' . json_encode($result));
                } else {
                    $result = $this->handlePaymentInterface->handlePaymentNotToken($orderIncrementId, $payOption);
                    $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
                    $resultJson->setData($result);
                    $this->_logger->info('-----------Request Data' . $orderIncrementId . ':' . json_encode($result));
                }
                return $resultJson;
            } elseif ($payOption == PaymentAttributeInterface::PAY_WITH_TOKEN
                || $payOption == PaymentAttributeInterface::PURCHASE_OTP) {
                $result = $this->handlePaymentInterface->handlePaymentWithToken($orderIncrementId, $payOption, $payType);
                $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
                $resultJson->setData($result);
                $this->_logger->info('-----------Request Data' . $orderIncrementId . ':' . json_encode($result));
                return $resultJson;
            }
        } catch (LocalizedException | \Exception $exception) {
            $this->_logger->debug("--- exception: " . $exception->getMessage() . " ---");
            $this->messageManager->addError(__("Sorry, something went wrong. Please try again later."));
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }
    }
}
