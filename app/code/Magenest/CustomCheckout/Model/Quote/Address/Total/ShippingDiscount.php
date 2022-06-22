<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 30/11/2021
 * Time: 08:57
 */

namespace Magenest\CustomCheckout\Model\Quote\Address\Total;


use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\SalesRule\Model\Quote\Discount as DiscountCollector;

class ShippingDiscount extends \Magento\SalesRule\Model\Quote\Address\Total\ShippingDiscount
{
    /**
     * @inheritdoc
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(Quote $quote, Total $total): array
    {
        $result = [];
        $amount = $total->getDiscountAmount();

        if ($amount != 0) {
            $description = (string)$total->getDiscountDescription() ?: '';
            $result = [
                'code' => DiscountCollector::COLLECTOR_TYPE_CODE,
                'title' => strlen($description) ? __($description) : __('Discount'),
                'value' => $amount
            ];
        }
        return $result;
    }
}
