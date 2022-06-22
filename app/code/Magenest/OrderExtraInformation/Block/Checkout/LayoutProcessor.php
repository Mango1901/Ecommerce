<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderExtraInformation\Block\Checkout;

use Magenest\Core\Helper\Data as CoreData;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Customer\Model\Session;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Class LayoutProcessor
 * @package Magenest\OrderExtraInformation\Block\Cart
 */
class LayoutProcessor implements LayoutProcessorInterface
{
    /**
     * @var CoreData
     */
    protected $_coreData;

    /**
     * @var CheckoutSession
     */
    protected $_checkoutSession;

    /**
     * @var CartRepositoryInterface
     */
    protected $_quoteRepository;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var CustomerRepository
     */
    protected $_customerRepository;

    /**
     * Constructor.
     *
     * @param CartRepositoryInterface $quoteRepository
     * @param CheckoutSession $checkoutSession
     * @param CoreData $coreData
     * @param CustomerRepository $customerRepository
     * @param Session $customerSession
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        CheckoutSession $checkoutSession,
        CoreData $coreData,
        CustomerRepository $customerRepository,
        Session $customerSession
    ) {
        $this->_coreData = $coreData;
        $this->_checkoutSession = $checkoutSession;
        $this->_quoteRepository = $quoteRepository;
        $this->_customerRepository = $customerRepository;
        $this->_customerSession = $customerSession;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {
        $children = [];
        $quote = $this->_quoteRepository->getActive($this->_checkoutSession->getQuoteId());

        if ($this->_coreData->getConfigValue('oei/customer_note/enable') && in_array('checkout', explode(',', $this->_coreData->getConfigValue('oei/customer_note/show_on')))) {
            $children['customer_note'] = [
                'component' => 'Magento_Ui/js/form/element/textarea',
                'dataScope' => 'additional-data.customer_note',
                'provider' => 'checkoutProvider',
                'value' => $quote->getCustomerNote(),
                'config' => [
                    'label' => __('Order Note'),
                    'template' => 'ui/form/field'
                ],
                'visible' => false
            ];
        }

//        if ($this->_coreData->getConfigValue('oei/delivery_date/enable')) {
//            $children['delivery_date'] = array_merge($children, [
//                'component' => 'Magento_Ui/js/form/element/date',
//                'dataScope' => 'additional-data.delivery_date',
//                'provider' => 'checkoutProvider',
//                'value' => $quote->getDeliveryDate(),
//                'options' => [
//                    'minDate' => '+0'
//                ],
//                'config' => [
//                    'label' => __('Delivery Date'),
//                    'template' => 'ui/form/field'
//                ]
//            ]);
//        }

        if ($this->_coreData->getConfigValue('oei/delivery_time/enable')) {
            $children['delivery_time'] = array_merge($children, [
                'component' => 'Magento_Ui/js/form/element/select',
                'dataScope' => 'additional-data.delivery_time',
                'provider' => 'checkoutProvider',
                'value' => $quote->getDeliveryTime(),
                'options' => [
                    [
                        'value' => 0,
                        'label' => __('In office hours'),
                    ],
                    [
                        'value' => 1,
                        'label' => __('Outside office hours'),
                    ]
                ],
                'config' => [
                    'label' => __('Delivery Time'),
                    'template' => 'ui/form/field'
                ]
            ]);
        }

        if ($this->_coreData->getConfigValue('oei/vat_invoice/enable')) {
            $defaultVATInvoice = [];
            if ($this->_customerSession->isLoggedIn()) {
                $customer = $this->_customerRepository->getById($this->_customerSession->getCustomerId());
                if ($customer->getCustomAttribute('default_vat_invoice')) {
                    $defaultVATInvoice = \Zend_Json::decode($customer->getCustomAttribute('default_vat_invoice')->getValue());
                }
            }

            $companyName = $quote->getCompanyName() ? $quote->getCompanyName() : $defaultVATInvoice['company_name'] ?? "";
            $taxCode = $quote->getTaxCode() ? $quote->getTaxCode() : $defaultVATInvoice['tax_code'] ?? "";
            $companyAddress = $quote->getCompanyAddress() ? $quote->getCompanyAddress() : $defaultVATInvoice['company_address'] ?? "";
            $companyEmail = $quote->getCompanyEmail() ? $quote->getCompanyEmail() : $defaultVATInvoice['company_email'] ?? "";

            $defaultVATInvoiceChecked = (bool)$quote->getCompanyName();
            $children = array_merge($children, ['vat_information_wrapper' => [
                'component' => 'uiCollection',
                'provider' => 'checkoutProvider',
                'config' => [
                    'template' => 'Magenest_OrderExtraInformation/checkout/shipping/information-wrapper'
                ],
                'additionalClasses' => 'vat-information',
                'children' => [
                    'save_vat_invoice' => [
                        'component' => 'Magento_Ui/js/form/element/abstract',
                        'dataScope' => 'additional-data.vat_invoice.save_vat_invoice',
                        'provider' => 'checkoutProvider',
//                        'visible' => false,
                        'value' => $defaultVATInvoiceChecked,
                        'config' => [
                            'label' => __('Invoice information is the same as receiving information'),
                            'template' => 'ui/form/field',
                            'elementTmpl' => 'ui/form/element/checkbox'
                        ]
                    ],
                    'company_name' => [
                        'component' => 'Magento_Ui/js/form/element/abstract',
                        'dataScope' => 'additional-data.vat_invoice.company_name',
                        'provider' => 'checkoutProvider',
                        'value' => $companyName,
                        'config' => [
                            'label' => __('Company Name'),
                            'template' => 'ui/form/field',
                            'elementTmpl' => 'ui/form/element/input',
                            'imports' => [
                                'visible' => '${ $.provider }:${ $.parentScope }.save_vat_invoice:checked'
                            ]
                        ],
                        'validation' => [
                            'required-entry' => true
                        ]
                    ],
                    'company_email' => [
                        'component' => 'Magento_Ui/js/form/element/abstract',
                        'dataScope' => 'additional-data.vat_invoice.company_email',
                        'provider' => 'checkoutProvider',
                        'value' => $companyEmail,
                        'config' => [
                            'label' => __('Company Email'),
                            'template' => 'ui/form/field',
                            'elementTmpl' => 'ui/form/element/input',
                            'imports' => [
                                'visible' => '${ $.provider }:${ $.parentScope }.save_vat_invoice:checked'
                            ]
                        ],
                        'validation' => [
                            'required-entry' => true,
                            'validate-email' => true
                        ]
                    ],
                    'tax_code' => [
                        'component' => 'Magento_Ui/js/form/element/abstract',
                        'dataScope' => 'additional-data.vat_invoice.tax_code',
                        'provider' => 'checkoutProvider',
                        'value' => $taxCode,
                        'config' => [
                            'label' => __('Tax Code'),
                            'template' => 'ui/form/field',
                            'elementTmpl' => 'ui/form/element/input',
                            'imports' => [
                                'visible' => '${ $.provider }:${ $.parentScope }.save_vat_invoice:checked'
                            ]
                        ],
                        'validation' => [
                            'required-entry' => true
                        ]
                    ],
                    'company_address' => [
                'component' => 'Magento_Ui/js/form/element/abstract',
                'dataScope' => 'additional-data.vat_invoice.company_address',
                'provider' => 'checkoutProvider',
                'value' => $companyAddress,
                'config' => [
                    'label' => __('Company Address'),
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/input',
                    'imports' => [
                        'visible' => '${ $.provider }:${ $.parentScope }.save_vat_invoice:checked'
                    ]
                ],
                'validation' => [
                    'required-entry' => true
                ]
            ]
                ]
            ]]);
        }

        if ($this->_coreData->getConfigValue('oei/customer_consign/enable')) {
            $defaultCustomerConsign = false;
            $children = array_merge($children, ["customer_consign_wrapper" => [
                'component' => 'uiCollection',
                'provider' => 'checkoutProvider',
                'config' => [
                    'template' => 'Magenest_OrderExtraInformation/checkout/shipping/information-wrapper'
                ],
                'children' => [
                    'save_customer_consign' => [
                        'component' => 'Magento_Ui/js/form/element/abstract',
                        'dataScope' => 'additional-data.customer_consign.save_customer_consign',
                        'provider' => 'checkoutProvider',
                        'visible' => false,
                        'value' => $defaultCustomerConsign,
                        'config' => [
                            'label' => __('Customer Consign'),
                            'template' => 'ui/form/field',
                            'elementTmpl' => 'ui/form/element/checkbox'
                        ]
                    ],
                    'telephone_customer_consign' => [
                        'component' => 'Magento_Ui/js/form/element/abstract',
                        'dataScope' => 'additional-data.customer_consign.telephone_customer_consign',
                        'provider' => 'checkoutProvider',
                        'value' => '',
                        'config' => [
                            'label' => __('Telephone Number Of Customer Consign'),
                            'template' => 'ui/form/field',
                            'elementTmpl' => 'ui/form/element/input',
                            'imports' => [
                                'visible' => '${ $.provider }:${ $.parentScope }.save_customer_consign:checked'
                            ]
                        ],
                        'validation' => [
                            'required-entry' => true,
                            'validate-telephone-require' => true
                        ]
                    ],
                ],
                'additionalClasses' => 'customer-consign-information'
            ]]);
        }

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']
        ['children']['after-form']['children']['additional-data'] = [
            'component' => 'Magenest_OrderExtraInformation/js/view/checkout/shipping/additional-information',
            'provider' => 'checkoutProvider',
            'config' => [
                'template' => 'Magenest_OrderExtraInformation/checkout/shipping/additional-information'
            ],
            'children' => $children,
            'additionalClasses' => 'company-vat-information'
        ];
        $addressField = &$jsLayout["components"]["checkout"]["children"]["steps"]["children"]["shipping-step"]["children"]["shippingAddress"]["children"]
        ["shipping-address-fieldset"]["children"];
        $addressField['country_id']['visible'] = false;
        $addressField['lastname']['sortOrder'] = 15;
        $addressField['company']['visible'] = false;
        $addressField['postcode']['visible'] = false;
        $addressField['postcode']['value'] = 100000;
        $addressField['street']['sortOrder'] = 300;
        $addressField['street']['label'] = __('Shipping Address');
        $addressField['telephone']['component'] = 'Magento_Checkout/js/form/element/obs-telephone';
        $addressField['telephone']['config']['template'] = 'Magento_Checkout/shipping-address/element/obs-telephone';
        if ($this->_customerSession->isLoggedIn()) {
            $addressField['telephone']['visible'] = true;
            $additionalClass = $addressField['street']['config']['additionalClasses'] ?? "";

            $addressField['street']['config']['additionalClasses'] = $additionalClass . " w-100";
        } else {
            $addressField['telephone']['visible'] = false;
        }
        return $jsLayout;
    }
}
