<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\PaymentEPay\Block\Customer;

use Magenest\PaymentEPay\Api\Data\HandlePaymentInterface;
use Magento\Framework\View\Element\Template;
use Magento\Checkout\Model\Session;

class InstallmentPayment extends Template
{
    /**
     * @var HandlePaymentInterface
     */
    protected $handlePaymentInterface;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;
    /**
     * @var Session
     */
    protected $_session;

    /**
     * InstallmentPayment constructor.
     * @param Session $session
     * @param HandlePaymentInterface $handlePaymentInterface
     * @param \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        Session $session,
        HandlePaymentInterface $handlePaymentInterface,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory,
        Template\Context $context,
        array $data = []
    ) {
        $this->_session = $session;
        $this->resultRedirectFactory = $redirectFactory;
        $this->handlePaymentInterface = $handlePaymentInterface;
        parent::__construct($context, $data);
    }

    public function getInstallmentPaymentList($amount)
    {
        return $this->handlePaymentInterface->handleInstallmentPaymentListing($amount);
    }

    public function getFormAction()
    {
        return 'installmentpayment';
    }
}
