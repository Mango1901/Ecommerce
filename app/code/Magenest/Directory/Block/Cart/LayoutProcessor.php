<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Block\Cart;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Checkout\Block\Checkout\AttributeMerger;
use Magenest\Directory\Helper\Data;

/**
 * Class LayoutProcessor
 * @package Magenest\Directory\Block\Cart
 */
class LayoutProcessor implements LayoutProcessorInterface
{
    /**
     * @var AttributeMerger
     */
    protected $_merger;

    /**
     * @var Data
     */
    protected $_dataHelper;

    /**
     * Constructor.
     *
     * @param AttributeMerger $merger
     * @param Data $dataHelper
     */
    public function __construct(
        AttributeMerger $merger,
        Data $dataHelper
    ) {
        $this->_merger = $merger;
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
        $elements = [
            'city_id' => [
                'visible' => true,
                'formElement' => 'select',
                'label' => __('City'),
                'options' => [],
                'value' => null
            ],
            'district_id' => [
                'visible' => true,
                'formElement' => 'select',
                'label' => __('District'),
                'options' => [],
                'value' => null
            ],
            'ward_id' => [
                'visible' => true,
                'formElement' => 'select',
                'label' => __('Ward'),
                'options' => [],
                'value' => null
            ]
        ];

        if (isset($jsLayout['components']['block-summary']['children']['block-shipping']['children']
            ['address-fieldsets']['children'])
        ) {
            $fieldSetPointer = &$jsLayout['components']['block-summary']['children']['block-shipping']
            ['children']['address-fieldsets']['children'];
            $fieldSetPointer = $this->_merger->merge($elements, 'checkoutProvider', 'shippingAddress', $fieldSetPointer);
            $fieldSetPointer['city_id']['config']['skipValidation'] = true;
            $fieldSetPointer['district_id']['config']['skipValidation'] = true;
            $fieldSetPointer['ward_id']['config']['skipValidation'] = true;
            $fieldSetPointer['postcode']['config']['visible'] = false;
            $fieldSetPointer['country_id']['config']['visible'] = false;
            $fieldSetPointer['region_id']['config']['componentDisabled'] = true;
        }

        if (isset($jsLayout['components']['checkoutProvider']['dictionaries']['country_id'])) {
            $countryList = $jsLayout['components']['checkoutProvider']['dictionaries']['country_id'];
            $vnCountry = $this->getCountryOption($countryList);
            $jsLayout['components']['checkoutProvider']['dictionaries']['country_id'] = $vnCountry;
        }

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
