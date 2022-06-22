<?php

namespace Magenest\FormatPrice\Plugin\Order;



class PriceCurrency
{
    public function beforeFormatPricePrecision(
        $subject,
        $price,
        $precision,
        $addBrackets = false
    ) {

        return [$price, 0, $addBrackets];
    }
}
