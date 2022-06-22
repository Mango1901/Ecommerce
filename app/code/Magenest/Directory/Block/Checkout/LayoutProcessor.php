<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Directory\Block\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magenest\Directory\Helper\Data;

/**
 * Class LayoutProcessor
 * @package Magenest\Directory\Block\Checkout
 */
class LayoutProcessor implements LayoutProcessorInterface
{
    /**
     * @var Data
     */
    protected $_dataHelper;

    /**
     * Constructor.
     *
     * @param Data $dataHelper
     */
    public function __construct(
        Data $dataHelper
    )
    {
        $this->_dataHelper = $dataHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {
        $jsLayout['components']['checkoutProvider']['dictionaries']['city_id'] = $this->getCityOptions();
        $jsLayout['components']['checkoutProvider']['dictionaries']['district_id'] = $this->getDistrictOptions();
        $jsLayout['components']['checkoutProvider']['dictionaries']['ward_id'] = $this->getWardOptions();
        if (isset($jsLayout['components']['checkoutProvider']['dictionaries']['country_id'])) {
            $countryList = $jsLayout['components']['checkoutProvider']['dictionaries']['country_id'];
            $vnCountry = $this->getCountryOption($countryList);
            $jsLayout['components']['checkoutProvider']['dictionaries']['country_id'] = $vnCountry;
        }

        $paymentLayout = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list'];

        foreach (array_keys($paymentLayout['children']) as $elementName) {
            if (strpos($elementName, '-form') !== false) {
                $paymentCode = str_replace('-form', '', $elementName);
                $formFields = array_replace_recursive(
                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']
                    ['children']['payments-list']['children'][$elementName]['children']['form-fields']['children'],
                    [
                        'city' => [
                            'visible' => false,
                        ],
                        'country_id' => [
                            'visible' => false,
                        ],
                        'city_id' => [
                            'component' => 'Magenest_Directory/js/form/element/city',
                            'label' => __('City'),
                            'config' => [
                                'template' => 'ui/form/field',
                                'elementTmpl' => 'ui/form/element/select',
                                'customEntry' => 'billingAddress' . $paymentCode . '.city',
                            ],
                            'validation' => [
                                'required-entry' => true,
                            ],
                            'filterBy' => [
                                'target' => '${ $.provider }:${ $.parentScope }.country_id',
                                'field' => 'country_id',
                            ],
                            'deps' => ['checkoutProvider'],
                            'imports' => [
                                'initialOptions' => 'index = checkoutProvider:dictionaries.city_id',
                                'setOptions' => 'index = checkoutProvider:dictionaries.city_id'
                            ]
                        ],
                        'district' => [
                            'visible' => false,
                        ],
                        'district_id' => [
                            'component' => 'Magenest_Directory/js/form/element/district',
                            'label' => __('District'),
                            'config' => [
                                'template' => 'ui/form/field',
                                'elementTmpl' => 'ui/form/element/select',
                                'customEntry' => 'billingAddress' . $paymentCode . '.district',
                            ],
                            'validation' => [
                                'required-entry' => true,
                            ],
                            'filterBy' => [
                                'target' => '${ $.provider }:${ $.parentScope }.city_id',
                                'field' => 'city_id',
                            ],
                            'deps' => ['checkoutProvider'],
                            'imports' => [
                                'initialOptions' => 'index = checkoutProvider:dictionaries.district_id',
                                'setOptions' => 'index = checkoutProvider:dictionaries.district_id'
                            ]
                        ],
                        'ward' => [
                            'visible' => false,
                        ],
                        'ward_id' => [
                            'component' => 'Magenest_Directory/js/form/element/ward',
                            'label' => __('Ward'),
                            'config' => [
                                'template' => 'ui/form/field',
                                'elementTmpl' => 'ui/form/element/select',
                                'customEntry' => 'billingAddress' . $paymentCode . '.ward',
                            ],
                            'validation' => [
                                'required-entry' => true,
                            ],
                            'filterBy' => [
                                'target' => '${ $.provider }:${ $.parentScope }.district_id',
                                'field' => 'district_id',
                            ],
                            'deps' => ['checkoutProvider'],
                            'imports' => [
                                'initialOptions' => 'index = checkoutProvider:dictionaries.ward_id',
                                'setOptions' => 'index = checkoutProvider:dictionaries.ward_id'
                            ]
                        ],
                        'telephone' => [
                            'validation' => [
                                'required-entry' => true,
                                'validate-telephone-require' => true
                            ],
                        ],
                        'postcode' => [
                            'visible' => false
                        ],
                        'region_id' => [
                            'visible' => false
                        ],
                        'street' => [
                            'sortOrder' => 100,
                            'label' => 'Shipping Address'
                        ]
                    ]
                );

                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']
                ['children']['payments-list']['children'][$elementName]['children']['form-fields']['children'] = $formFields;
            }
        }

        $cartItem = $jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['cart_items'];
        unset($jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['cart_items']);
        $cartItem['displayArea'] = "cart-items";
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['cart-items'] = $cartItem;

        $addressField = &$jsLayout["components"]["checkout"]["children"]["steps"]["children"]["shipping-step"]["children"]["shippingAddress"]["children"]
        ["shipping-address-fieldset"]["children"];

        $childrenDirectory = [
            'city_id' => [
                'component' => 'Magenest_Directory/js/form/element/city',
                'config' => [
                    // customScope is used to group elements within a single form (e.g. they can be validated separately)
                    'customScope' => 'shippingAddress.city_id',
                    'customEntry' => null,
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'Magenest_Directory/checkout/shipping/directory',
                    'imports' => [
                        'initialOptions' => 'index = checkoutProvider:dictionaries.city_id',
                        'setOptions' => 'index = checkoutProvider:dictionaries.city_id',
                    ]
                ],
                'dataScope' => 'shippingAddress.city_id',
                'label' => __('City'),
                'provider' => 'checkoutProvider',
                'sortOrder' => 10,
                'validation' => [
                    'required-entry' => true
                ],
                'options' => [],
                'filterBy' => [
                    'target' => '${ $.provider }:${ $.parentScope }.country_id',
                    'field' => 'country_id'
                ],
                'customEntry' => 'shippingAddress.city',
                'visible' => true,
                'value' => '' // value field is used to set a default value of the attribute
            ],
            'district_id' => [
                'component' => 'Magenest_Directory/js/form/element/district',
                'config' => [
                    // customScope is used to group elements within a single form (e.g. they can be validated separately)
                    'customScope' => 'shippingAddress.district',
                    'customEntry' => null,
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'Magenest_Directory/checkout/shipping/directory',
                    'imports' => [
                        'initialOptions' => 'index = checkoutProvider:dictionaries.district_id',
                        'setOptions' => 'index = checkoutProvider:dictionaries.district_id',
                    ]
                ],
                'dataScope' => 'shippingAddress.district_id',
                'label' => __('District'),
                'provider' => 'checkoutProvider',
                'sortOrder' => 20,
                'validation' => [
                    'required-entry' => true
                ],
                'options' => [],
                'filterBy' => [
                    'target' => '${ $.provider }:${ $.parentScope }.city_id',
                    'field' => 'city_id'
                ],
                'customEntry' => 'shippingAddress.district',
                'visible' => true,
                'value' => '' // value field is used to set a default value of the attribute
            ],
            'ward_id' => [
                'component' => 'Magenest_Directory/js/form/element/ward',
                'config' => [
                    // customScope is used to group elements within a single form (e.g. they can be validated separately)
                    'customScope' => 'shippingAddress.ward',
                    'customEntry' => null,
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'Magenest_Directory/checkout/shipping/directory',
                    'imports' => [
                        'initialOptions' => 'index = checkoutProvider:dictionaries.ward_id',
                        'setOptions' => 'index = checkoutProvider:dictionaries.ward_id',
                    ]
                ],
                'dataScope' => 'shippingAddress.ward_id',
                'label' => __('Ward'),
                'provider' => 'checkoutProvider',
                'sortOrder' => 30,
                'validation' => [
                    'required-entry' => true
                ],
                'options' => [],
                'filterBy' => [
                    'target' => '${ $.provider }:${ $.parentScope }.district_id',
                    'field' => 'district_id'
                ],
                'customEntry' => 'shippingAddress.ward',
                'visible' => true,
                'value' => '' // value field is used to set a default value of the attribute
            ]

        ];

        $addressField['directory-information'] = [
            'component' => 'Magenest_Directory/js/view/checkout/shipping/directory-information',
            'provider' => 'checkoutProvider',
            'config' => [
                'template' => 'Magenest_Directory/checkout/shipping/directory-wrapper',
                'imports' => [
                    'cityField' => '${ $.provider }:${ $.parentScope }.city_id',
                    'handleChangesCity' => '${ $.provider }:shippingAddress.city',
                    'districtField' => '${ $.provider }:${ $.parentScope }.district_id',
                    'handleChangesDistrict' => '${ $.provider }:shippingAddress.district',
                    'wardField' => '${ $.provider }:${ $.parentScope }.ward_id',
                    'handleChangesWard' => '${ $.provider }:shippingAddress.ward',
                    '__disableTmpl' => [
                        'cityField' => false,
                        'handleChanges' => false,
                        'districtField' => false,
                        'handleChangesDistrict' => false,
                        'wardField' => false,
                        'handleChangesWard' => false
                    ],
                ],
            ],
            'children' => $childrenDirectory,
            'additionalClasses' => 'directory-information'
        ];

        return $jsLayout;
    }

    /**
     * @param $countryList
     * @param string $value
     * @return array
     */
    public function getCountryOption($countryList, $value='VN')
    {
        $result = [];
        foreach ($countryList as $country) {
            if (isset($country['value']) && $country['value'] == $value) {
                $result[] = $country;
            }
        }

        return $result;
    }

    /**
     * Get city options
     *
     * @return array
     */
    public function getCityOptions()
    {
        return $this->_dataHelper->getCityOptions();
    }

    /**
     * Get district options
     *
     * @return array
     */
    protected function getDistrictOptions()
    {
        return $this->_dataHelper->getDistrictOptions();
    }

    /**
     * Get ward options
     *
     * @return array
     */
    protected function getWardOptions()
    {
        return $this->_dataHelper->getWardOptions();
    }
}
