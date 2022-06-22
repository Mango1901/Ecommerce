<?php

namespace Magenest\CustomTableRate\Plugin\Model\Cart;

use Magento\Quote\Api\Data\ShippingMethodInterface;
use Magento\Quote\Model\Cart\ShippingMethodConverter as CartShippingMethodConverter;
use Magento\Quote\Model\Quote\Address\Rate;

class ShippingMethodConverter
{
    /**
     * @param CartShippingMethodConverter $subject
     * @param ShippingMethodInterface $result
     * @param Rate $rateModel
     * @param string $quoteCurrencyCode
     * @return ShippingMethodInterface
     */
    public function afterModelToDataObject($subject, $result, $rateModel, $quoteCurrencyCode)
    {
        $extensionAttributes = $result->getExtensionAttributes();
        if ($extensionAttributes && ($originalPrice = $rateModel->getOriginalPrice())) {
            $extensionAttributes->setOriginalPrice($originalPrice); // custom field value set
            $result->setExtensionAttributes($extensionAttributes);
        }
        return $result;
    }
}
