<?php
namespace Magenest\SocialLogin\Model\Config;

/**
 * Class SocialShare
 * @package Magenest\SocialLogin\Model\Config
 */
class SocialShare implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'facebook',
                'label' => __('Facebook'),
            ],
            [
                'value' => 'zalo',
                'label' => __('Zalo'),
            ]
        ];
    }
}
