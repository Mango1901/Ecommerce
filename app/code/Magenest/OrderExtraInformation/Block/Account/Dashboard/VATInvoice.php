<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderExtraInformation\Block\Account\Dashboard;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Data\Form\FormKey;

/**
 * Class VATInvoice
 * @package Magenest\OrderExtraInformation\Block\Account\Dashboard
 */
class VATInvoice extends Template
{
    /**
     * @var CurrentCustomer
     */
    protected $_currentCustomer;

    /**
     * @var FormKey
     */
    protected $_formKey;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param CurrentCustomer $currentCustomer
     * @param FormKey $formKey
     * @param array $data
     */
    public function __construct(
        Context $context,
        CurrentCustomer $currentCustomer,
        FormKey $formKey,
        array $data = []
    )
    {
        $this->_currentCustomer = $currentCustomer;
        $this->_formKey = $formKey;
        parent::__construct($context, $data);
    }

    /**
     * Get the logged in customer
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomer()
    {
        try {
            return $this->_currentCustomer->getCustomer();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * Get default vat invoice
     *
     * @return mixed|null
     * @throws \Zend_Json_Exception
     */
    public function getDefaultVATInvoice()
    {
        if ($this->getCustomer() && $this->getCustomer()->getCustomAttribute('default_vat_invoice')) {
            return $this->getCustomer()->getCustomAttribute('default_vat_invoice')->getValue();
        }

        return null;
    }

    /**
     * Get form option
     *
     * @return array
     */
    public function getFormOptions()
    {
        $options = [
            'saveUrl' => $this->getUrl('oei/vat/post', array('action' => 'save')),
            'has_default_information' => false,
            'formKey' => $this->_formKey->getFormKey(),
            'company_name' => '',
            'tax_code' => '',
            'company_address' => '',
            'company_email' => ''
        ];

        if ($defaultVATInvoice = $this->getDefaultVATInvoice()) {
            $defaultVATInvoice = \Zend_Json::decode($defaultVATInvoice);
            $options['company_name'] = $defaultVATInvoice['company_name'] ?? null;
            $options['tax_code'] = $defaultVATInvoice['tax_code'] ?? null;
            $options['company_address'] = $defaultVATInvoice['company_address'] ?? null;
            $options['company_email'] = $defaultVATInvoice['company_email'] ?? null;
            $options['has_default_information'] = true;
            $options['removeUrl'] = $this->getUrl('oei/vat/post');
        }

        return $options;
    }
}
