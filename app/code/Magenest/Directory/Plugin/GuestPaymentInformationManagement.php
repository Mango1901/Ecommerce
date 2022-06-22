<?php
namespace Magenest\Directory\Plugin;

use Magento\Checkout\Api\GuestPaymentInformationManagementInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;

class GuestPaymentInformationManagement
{
    /**
     * @param GuestPaymentInformationManagementInterface $subject
     * @param $cartId
     * @param $email
     * @param PaymentInterface $paymentMethod
     * @param AddressInterface|null $billingAddress
     */
    public function beforeSavePaymentInformationAndPlaceOrder(
        GuestPaymentInformationManagementInterface $subject,
        $cartId,
        $email,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    ) {
        if ($billingAddress) {
            $customAttr = ['city_id', 'district', 'district_id', 'ward', 'ward_id'];
            foreach ($customAttr as $attr) {
                $attribute = $billingAddress->getCustomAttribute($attr);
                $value = isset($attribute) && $attribute->getValue() !== null
                    ? $attribute->getValue()
                    : null;
                if (isset($value) && isset($value['value'])) {
                    $billingAddress->setCustomAttribute($attr, $value['value']);
                }
            }
        }
    }
}
