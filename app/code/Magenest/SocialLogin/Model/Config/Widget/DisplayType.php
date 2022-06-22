<?php


namespace Magenest\SocialLogin\Model\Config\Widget;


/**
 * Class DisplayType
 * @package Magenest\SocialLogin\Model\Config\Widget
 */
class DisplayType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     *
     */
    const SLIDER    = 1;
    /**
     *
     */
    const LIST_ICON = 2;

    /**
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::SLIDER,
                'label' => __('Slider'),
            ],
            [
                'value' => self::LIST_ICON,
                'label' => __('List icon'),
            ],
        ];
    }
}
