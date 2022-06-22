<?php

namespace Magenest\Customer\Plugin\Block\Checkout;

use Magenest\Customer\Helper\ConfigHelper;
use Magento\Checkout\Block\Checkout\LayoutProcessor;

class NameLayoutProcessor
{
    private $configHelper;

    public function __construct(
        ConfigHelper $configHelper
    )
    {
        $this->configHelper = $configHelper;
    }

    /**
     * @param LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        LayoutProcessor $subject,
        array           $jsLayout
    ) {
        $shippingConfiguration = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];

        if ($this->configHelper->isEnabledFullNameInstead()) {
            $shippingConfiguration = $this->setFullName($shippingConfiguration);
        }

        return $jsLayout;
    }

    /**
     * Set "Full Name"
     * @param array $shippingConfiguration
     * @return array
     */
    private function setFullName(array $shippingConfiguration)
    {

        $shippingConfiguration['fullname'] = [
            'component' => 'Magenest_Customer/js/checkout/abstract',
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input',
                'options' => [],
                'id' => 'fullname'
            ],
            'dataScope' => 'shippingAddress.fullname',
            'label' => __('Full Name'),
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => [
                'required-entry' => true
            ],
            'sortOrder' => 0,
            'id' => 'fullname',
        ];

        return $this->makeTrackableFirstLastName($shippingConfiguration);
    }


    /**
     * Track firstName and lastName
     * @param array $shippingConfiguration
     * @return array
     */
    private function makeTrackableFirstLastName(array $shippingConfiguration)
    {
        $shippingConfiguration['firstname']['tracks']['value'] = true;
        $shippingConfiguration['firstname']['visible'] = false;
        $shippingConfiguration['lastname']['tracks']['value'] = true;
        $shippingConfiguration['lastname']['visible'] = false;

        return $shippingConfiguration;
    }
}
