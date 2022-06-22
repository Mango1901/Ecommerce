<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\PaymentEPay\Block\Customer;

use Magento\Framework\View\Element\Template;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Vault\Model\CustomerTokenManagement;
use Magenest\PaymentEPay\Api\Data\PaymentAttributeInterface;


class CardRenderer extends Template
{
    /**
     * @var PaymentTokenInterface[]
     */
    private $customerTokens;

    /**
     * @var CustomerTokenManagement
     */
    private $customerTokenManagement;

    /**
     * PaymentTokens constructor.
     * @param Template\Context $context
     * @param CustomerTokenManagement $customerTokenManagement
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CustomerTokenManagement $customerTokenManagement,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerTokenManagement = $customerTokenManagement;
    }

    /**
     * @return PaymentTokenInterface[]
     */
    public function getPaymentTokens(): array
    {
        return $this->getCustomerTokens();
    }

    /**
     * Get customer session tokens
     * @return PaymentTokenInterface[]
     */
    private function getCustomerTokens(): array
    {
        $arrayTokens = [];
        if (empty($this->customerTokens)) {
            $this->customerTokens = $this->customerTokenManagement->getCustomerSessionTokens();
            foreach ($this->customerTokens as $tokenEpay) {
                if ($tokenEpay->getData('payment_method_code') === PaymentAttributeInterface::CODE_VNPT_EPAY) {
                    $arrayTokens[] = $tokenEpay;
                }
            }
        }
        return $arrayTokens;
    }

    public function getIconUrl($type): string
    {
        $cardIcon = '';
        switch ($type){
            case 'VI':
                $cardIcon = $this->getViewFileUrl('Magenest_PaymentEPay::images/vi.png');
                break;
            case 'MC':
                $cardIcon = $this->getViewFileUrl('Magenest_PaymentEPay::images/mc.png');
                break;
            case 'JCB':
                $cardIcon = $this->getViewFileUrl('Magenest_PaymentEPay::images/jcb.png');
                break;
            case 'DC':
                $cardIcon = $this->getViewFileUrl('Magenest_PaymentEPay::images/napas.png');
                break;
        }
        return $cardIcon;
    }

    /**
     * Checks if customer tokens exists
     * @return bool
     */
    public function isExistsCustomerTokens(): bool
    {
        return !empty($this->getCustomerTokens());
    }
}
