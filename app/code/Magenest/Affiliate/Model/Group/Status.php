<?php


namespace Magenest\Affiliate\Model\Group;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Status
 * @package Magenest\Affiliate\Model\Group
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
