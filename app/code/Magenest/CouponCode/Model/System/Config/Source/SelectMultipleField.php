<?php
namespace Magenest\CouponCode\Model\System\Config\Source;

class SelectMultipleField implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'coupon_code', 'label' => __('Coupon Code')],
            ['value' => 'description', 'label' => __('Description')],
            ['value' => 'uses_per_coupon', 'label' => __('Uses Per Coupon')],
            ['value' => 'uses_per_customer', 'label' => __('Uses Per Customer')],
            ['value' => 'expiring', 'label' => __('Expiring')]
        ];
    }
}
