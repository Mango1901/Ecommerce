<?php


namespace Magenest\SocialLogin\Model\Config;


/**
 * Class DisplayOn
 * @package Magenest\SocialLogin\Model\Config
 */
class DisplayOn implements \Magento\Framework\Option\ArrayInterface
{
    /**
     *
     */
    const CREATE_ACCOUNT_PAGE = 1;
    /**
     *
     */
    const CHECKOUT_PAGE   = 2;
    /**
     *
     */
    const COMMENT_PRODUCT = 3;

    /**
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::CREATE_ACCOUNT_PAGE,
                'label' => __('Create Account Page'),
            ],
            [
                'value' => self::CHECKOUT_PAGE,
                'label' => __('Checkout Page'),
            ],
            [
                'value' => self::COMMENT_PRODUCT,
                'label' => __('Comment Product'),
            ]
        ];
    }
}
