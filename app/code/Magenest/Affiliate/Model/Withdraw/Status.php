<?php


namespace Magenest\Affiliate\Model\Withdraw;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Status
 * @package Magenest\Affiliate\Model\Withdraw
 */
class Status implements ArrayInterface
{
    const PENDING = 1;
    const COMPLETE = 2;
    const CANCEL = 3;
    const FAILED = 4;

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
            self::PENDING => __('Pending'),
            self::COMPLETE => __('Complete'),
            self::CANCEL => __('Cancel'),
            self::FAILED => __('Failed')
        ];
    }
}
