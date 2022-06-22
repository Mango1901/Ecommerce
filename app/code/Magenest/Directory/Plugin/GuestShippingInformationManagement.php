<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Directory extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Directory
 */

namespace Magenest\Directory\Plugin;

use Magento\Checkout\Api\Data\ShippingInformationInterface;

class GuestShippingInformationManagement
{
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Api\GuestShippingInformationManagementInterface $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        $customAttr = ['city_id', 'district', 'district_id', 'ward', 'ward_id'];
        $addressShipping = $addressInformation->getShippingAddress();
        foreach ($customAttr as $attr) {
            $attribute = $addressShipping->getCustomAttribute($attr);
            $value = isset($attribute) && $attribute->getValue() !== null
                ? $attribute->getValue()
                : null;
            if (isset($value) && isset($value['value'])) {
                $addressShipping->setCustomAttribute($attr, $value['value']);
            }
        }

        return [$cartId, $addressInformation];
    }
}
