<?php


namespace Magenest\Affiliate\Model\Account;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Status
 * @package Magenest\Affiliate\Model\Account
 */
class Status implements ArrayInterface
{
    const ACTIVE = 1;
    const INACTIVE = 2;
    const NEED_APPROVED = 3;

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::ACTIVE,
                'label' => __('Active')
            ],
            [
                'value' => self::INACTIVE,
                'label' => __('Inactive')
            ],
            [
                'value' => self::NEED_APPROVED,
                'label' => __('Need Approved')
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
            self::ACTIVE => __('Active'),
            self::INACTIVE => __('Inactive'),
            self::NEED_APPROVED => __('Need Approved')
        ];
    }
}
