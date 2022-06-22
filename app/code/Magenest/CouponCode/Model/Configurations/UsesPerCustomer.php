<?php
namespace Magenest\CouponCode\Model\Configurations;

class UsesPerCustomer extends AbstractFields
{
    public const CODE = "usage_per_customer";

    /**
     * @inheritdoc
     */
    public function apply($rules)
    {
        $customerId = $this->getCustomer()->getId();
        if ($customerId && $this->getConfigurationFieldByCode(self::CODE)) {
            $rules->getSelect()->where('(coupon_usage.customer_id=' . $customerId . '
			AND coupon_usage.times_used < main_table.uses_per_customer )
			OR coupon_usage.customer_id IS NULL
			OR rule_coupons.usage_per_customer IS NULL');
        }
        return $rules;
    }
}
