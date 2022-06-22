<?php


namespace Magenest\Directory\Plugin;


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
        $address = $addressInformation->getShippingAddress();
        if (!$address || (!$address->getCity() && !$address->getCityId())) {
            throw new StateException(__('The shipping address is missing. Check city or district or ward then set the address and try again.'));
        }
        return [$cartId, $addressInformation];
    }
}
