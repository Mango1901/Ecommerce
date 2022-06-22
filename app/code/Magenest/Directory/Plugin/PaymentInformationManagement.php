<?php


namespace Magenest\Directory\Plugin;


use Magento\Checkout\Api\PaymentInformationManagementInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;

class PaymentInformationManagement
{
    /**
     * @param PaymentInformationManagementInterface $subject
     * @param $cartId
     * @param PaymentInterface $paymentMethod
     * @param AddressInterface|null $billingAddress
     */
    public function beforeSavePaymentInformationAndPlaceOrder(
        PaymentInformationManagementInterface $subject,
        $cartId,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    ) {
        if($billingAddress) {
            $customAttr = ['city_id', 'district', 'district_id', 'ward', 'ward_id'];
            foreach ($customAttr as $attr) {
                if ($billingAddress->getCustomAttribute($attr) !== null && $billingAddress->getCustomAttribute($attr)->getValue()['value']) {
                    $billingAddress->setCustomAttribute($attr, $billingAddress->getCustomAttribute($attr)->getValue()['value']);
                }
            }
        }
    }
}
