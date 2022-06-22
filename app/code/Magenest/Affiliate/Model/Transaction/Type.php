<?php


namespace Magenest\Affiliate\Model\Transaction;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Type
 * @package Magenest\Affiliate\Model\Transaction
 */
class Type implements ArrayInterface
{
    const COMMISSION = 1;
    const PAID = 2;
    const ADMIN = 3;
    const STORE_CREDIT = 4;

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];

        foreach ($this->getOptionHash() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $options;
    }

    /**
     * @return array
     */
    public function getOptionHash()
    {
        return [
            self::COMMISSION => __('Commission'),
            self::PAID => __('Paid'),
            self::ADMIN => __('Admin'),
            self::STORE_CREDIT => __('Store Credit')
        ];
    }
}
