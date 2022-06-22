<?php


namespace Magenest\Affiliate\Model\Campaign;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Status
 * @package Magenest\Affiliate\Model\Campaign
 */
class Status implements ArrayInterface
{
    const ENABLED = 1;
    const DISABLED = 2;

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::ENABLED,
                'label' => __('Enabled')
            ],
            [
                'value' => self::DISABLED,
                'label' => __('Disabled')
            ],
        ];

        return $options;
    }

    /**
     * @return array
     */
    public function toOptionHash()
    {
        return [
            self::ENABLED => __('Enabled'),
            self::DISABLED => __('Disabled')
        ];
    }
}
