<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 10/01/2022
 * Time: 16:19
 */

namespace Magenest\Directory\Model\Rule\Condition;

/**
 * Class Address
 * @package Magenest\Directory\Model\Rule\Condition
 */
class Address extends \Magento\SalesRule\Model\Rule\Condition\Address
{
    /**
     * @var \Magenest\Directory\Helper\Data
     */
    protected $vietnameseDirectory;

    /**
     * Address constructor.
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Directory\Model\Config\Source\Country $directoryCountry
     * @param \Magento\Directory\Model\Config\Source\Allregion $directoryAllregion
     * @param \Magento\Shipping\Model\Config\Source\Allmethods $shippingAllmethods
     * @param \Magento\Payment\Model\Config\Source\Allmethods $paymentAllmethods
     * @param \Magenest\Directory\Helper\Data $vietnameseDirectory
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Directory\Model\Config\Source\Country $directoryCountry,
        \Magento\Directory\Model\Config\Source\Allregion $directoryAllregion,
        \Magento\Shipping\Model\Config\Source\Allmethods $shippingAllmethods,
        \Magento\Payment\Model\Config\Source\Allmethods $paymentAllmethods,
        \Magenest\Directory\Helper\Data $vietnameseDirectory,
        array $data = [])
    {
        $this->vietnameseDirectory = $vietnameseDirectory;
        parent::__construct($context, $directoryCountry, $directoryAllregion, $shippingAllmethods, $paymentAllmethods, $data);
    }

    /**
     * @return $this|\Magento\SalesRule\Model\Rule\Condition\Address
     */
    public function loadAttributeOptions()
    {
        $attributes = [
            'base_subtotal_with_discount' => __('Subtotal (Excl. Tax)'),
            'base_subtotal_total_incl_tax' => __('Subtotal (Incl. Tax)'),
            'base_subtotal' => __('Subtotal'),
            'total_qty' => __('Total Items Quantity'),
            'weight' => __('Total Weight'),
            'payment_method' => __('Payment Method'),
            'shipping_method' => __('Shipping Method'),
            'postcode' => __('Shipping Postcode'),
            'city_id'  => __('Shipping City'),
            'region' => __('Shipping Region'),
            'region_id' => __('Shipping State/Province'),
            'country_id' => __('Shipping Country'),
//            'district_id'  => __('District'),
//            'ward_id'  => __('Ward'),
        ];

        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @return string
     */
    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'base_subtotal':
            case 'base_subtotal_total_incl_tax':
            case 'weight':
            case 'total_qty':
                return 'numeric';

            case 'shipping_method':
            case 'payment_method':
            case 'country_id':
            case 'region_id':
                return 'select';
            case 'city_id':
//            case 'district_id':
//            case 'ward_id':
                return 'multiselect';
        }
        return 'string';
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case 'shipping_method':
            case 'payment_method':
            case 'country_id':
            case 'region_id':
                return 'select';
            case 'city_id':
                return 'multiselect';
        }
        return 'text';
    }

    /**
     * @return array|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                case 'city_id':
                    $options = $this->vietnameseDirectory->getCityOptions();
                    break;

                case 'country_id':
                    $options = $this->_directoryCountry->toOptionArray();
                    break;

                case 'region_id':
                    $options = $this->_directoryAllregion->toOptionArray();
                    break;

                case 'shipping_method':
                    $options = $this->_shippingAllmethods->toOptionArray();
                    break;

                case 'payment_method':
                    $options = $this->_paymentAllmethods->toOptionArray();
                    break;
                default:
                    $options = [];
            }
            $this->setData('value_select_options', $options);
        }

        return $this->getData('value_select_options');
    }
}
