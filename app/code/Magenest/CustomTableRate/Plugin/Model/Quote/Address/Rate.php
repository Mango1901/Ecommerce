<?php

namespace Magenest\CustomTableRate\Plugin\Model\Quote\Address;

use Magento\Quote\Model\Quote\Address\Rate as QuoteRate;
use Magento\Quote\Model\Quote\Address\RateResult\AbstractResult;
use Magento\Quote\Model\Quote\Address\RateResult\Method;

/**
 * Class Rate
 *
 * Used for plugin add field original price output for rate
 */
class Rate
{
    /**
     * @param QuoteRate $subject
     * @param QuoteRate $result
     * @param AbstractResult $rate
     * @return QuoteRate
     */
    public function afterImportShippingRate($subject, $result, $rate)
    {
        if ($rate instanceof Method) {
            if ($originalPrice = $rate->getOriginalPrice()) {
                $result->setOriginalPrice($originalPrice);
            }
        }
        return $result;
    }
}
