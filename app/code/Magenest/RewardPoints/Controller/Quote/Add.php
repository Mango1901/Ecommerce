<?php

namespace Magenest\RewardPoints\Controller\Quote;

use Magenest\RewardPoints\Model\AccountFactory;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Add
 *
 * @package Magenest\RewardPoints\Controller\Quote
 */
class Add extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magenest\RewardPoints\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var CurrentCustomer
     */
    protected $_currentCustomer;

    protected $cartHelper;

    /**
     * @var AccountFactory
     */
    protected $_accountFactory;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var \Magenest\RewardPoints\Helper\AddPoint
     */
    protected $addPoint;

    /**
     * Add constructor.
     * @param \Magenest\RewardPoints\Helper\Data $helper
     * @param Context $context
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param AccountFactory $accountFactory
     * @param CurrentCustomer $currentCustomer
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magenest\RewardPoints\Helper\AddPoint $addPoint
     */
    public function __construct(
        \Magenest\RewardPoints\Helper\Data $helper,
        Context $context,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Checkout\Model\Session $checkoutSession,
        AccountFactory $accountFactory,
        CurrentCustomer $currentCustomer,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magenest\RewardPoints\Helper\AddPoint $addPoint
    ) {
        parent::__construct($context);
        $this->_helper            = $helper;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->cart               = $cart;
        $this->_currentCustomer   = $currentCustomer;
        $this->_accountFactory    = $accountFactory;
        $this->cartHelper         = $cartHelper;
        $this->checkoutSession    = $checkoutSession;
        $this->addPoint           = $addPoint;
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $point          = $this->getRequest()->getParam('rewardpoints_quantity');
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $this->addPoint->addPoint($point);

        return $resultRedirect->setPath('checkout/index/');
    }
}
