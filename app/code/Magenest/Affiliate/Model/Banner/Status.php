<?php


namespace Magenest\Affiliate\Model\Banner;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Status
 * @package Magenest\Affiliate\Model\Banner
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
}
