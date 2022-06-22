<?php

namespace Magenest\PaymentEPay\Controller\Payment;

use Magenest\PaymentEPay\Api\Data\HandlePaymentInterface;
use Magenest\PaymentEPay\Helper\Data;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Class Response
 * @package Magenest\PaymentEPay\Controller\Order
 */
class ResponseIS extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    protected $quoteRepository;

    protected $cartManagement;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var CommandPoolInterface
     */
    protected $commandPool;

    protected $_logger;

    /**
     * @var HandlePaymentInterface
     */
    private $handlePaymentInterface;

    /**
     * Response constructor.
     * @param Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param CartRepositoryInterface $cartRepository
     * @param Data $helperData
     * @param CommandPoolInterface $commandPool
     * @param \Magenest\PaymentEPay\Logger\Logger $logger
     * @param CartManagementInterface $cartManagement
     */
    public function __construct(
        Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        CartRepositoryInterface $cartRepository,
        Data $helperData,
        CommandPoolInterface $commandPool,
        \Magenest\PaymentEPay\Logger\Logger $logger,
        CartManagementInterface $cartManagement,
        HandlePaymentInterface $handlePaymentInterface
    ) {
        parent::__construct($context);
        $this->orderFactory    = $orderFactory;
        $this->helperData = $helperData;
        $this->commandPool     = $commandPool;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $cartRepository;
        $this->_logger = $logger;
        $this->cartManagement = $cartManagement;
        $this->handlePaymentInterface = $handlePaymentInterface;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $responseParams = $this->getRequest()->getParams();
            $orderCurrent = $this->checkoutSession->getLastRealOrder();
            $vpcOrderInfo = $orderCurrent->getIncrementId();
            $order = $this->orderFactory->create()->loadByIncrementId($vpcOrderInfo);
            $this->checkoutSession->unsInstallmentPaymentValue();
            if (!empty($responseParams)) {
                $check = $this->helperData->checkToken($responseParams);
                if ($check) {
                    $amount = $this->getRequest()->getParam('amount', '0');
                    if ($order->getId()
                        && (floatval($amount) === floatval($order->getBaseGrandTotal()) || floatval($amount) <= floatval($order->getBaseGrandTotal()))) {
                        $payment = $order->getPayment();
                        if ($responseParams['resultCd'] === "00_000") {
                            $this->commandPool->get('order')->execute(
                                [
                                    'order' => $order,
                                    'payment' => $payment,
                                    'response' => $responseParams,
                                    'amount' => $payment->formatAmount($order->getBaseGrandTotal()),
                                    'amountIS' => '₫'.$payment->formatAmount($amount)
                                ]
                            );
                            $this->commandPool->get('complete')->execute(
                                [
                                    'order' => $order,
                                    'amount' => $payment->formatAmount($order->getBaseGrandTotal()),
                                    'amountIS' => '₫'.$payment->formatAmount($amount)
                                ]
                            );
                            return $this->resultRedirectFactory->create()->setPath('checkout/onepage/success');
                        } else {
                            $this->messageManager->addError(__("Your transaction has been canceled"));
                            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
                        }
                    }
                } else {
                    $this->messageManager->addError(__("Invalid Merchant Token."));
                    return $this->resultRedirectFactory->create()->setPath('checkout/onepage/failure');
                }
            } else {
                $this->messageManager->addError(__("Sorry, something went wrong. Please try again later."));
                return $this->resultRedirectFactory->create()->setPath('checkout/onepage/failure');
            }
        } catch (LocalizedException $exception) {
            $this->messageManager->addError(__("Sorry, something went wrong. Please try again later."));
            return $this->resultRedirectFactory->create()->setPath('checkout/onepage/failure');
        } catch (\Exception $exception) {
            $this->messageManager->addError(__("Sorry, something went wrong. Please try again later."));
            return $this->resultRedirectFactory->create()->setPath('checkout/onepage/failure');
        }
    }
}
