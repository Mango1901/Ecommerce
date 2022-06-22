<?php
namespace Magenest\CouponCode\Model\Configurations;

class UsesPerCoupon extends AbstractFields
{
    public const CODE = "usage_limit";

    /**
     * @inheritdoc
     */
    public function apply($rules)
    {
        if ($this->getConfigurationFieldByCode(self::CODE)) {
            $rules->getSelect()->where('coupon.times_used < coupon.usage_limit OR main_table.uses_per_coupon = 0');
        }
        return $rules;
    }
}
