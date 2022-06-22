<?php

namespace Magenest\PaymentEPay\Model\Config\Source;

/**
 * Class Locale
 * @package Magenest\OnePay\Model\Config\Source
 */
class Locale implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'vn', 'label' => __('Vietnamese')],
            ['value' => 'en', 'label' => __('English')]
        ];
    }

    /**
     * Get options in "key-value" format
     * @return array
     */
    public function toArray()
    {
        return [
            0 => __('Vietnamese'),
            1 => __('English')
        ];
    }
}
