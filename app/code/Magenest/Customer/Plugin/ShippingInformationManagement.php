<?php

namespace Magenest\Customer\Plugin;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Api\ShippingInformationManagementInterface;
use Magento\Framework\Exception\StateException;

class ShippingInformationManagement
{

    /**
     * @param ShippingInformationManagementInterface $subject
     * @param $cartId
     * @param ShippingInformationInterface $addressInformation
     * @return array
     */
    public function beforeSaveAddressInformation(
        ShippingInformationManagementInterface $subject,
                                               $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        $shippingAddress = $addressInformation->getShippingAddress();
        $fullNameShippingAddress = $shippingAddress->getFirstname() . ' ' . $shippingAddress->getLastname();
        $shippingAddress->setData('fullname', $fullNameShippingAddress);
        $billingAddress = $addressInformation->getBillingAddress();
        $fullNameBillingAddress = $billingAddress->getFirstname() . ' ' . $billingAddress->getLastname();
        $billingAddress->setData('fullname', $fullNameBillingAddress);
        return [$cartId, $addressInformation];
    }
}
