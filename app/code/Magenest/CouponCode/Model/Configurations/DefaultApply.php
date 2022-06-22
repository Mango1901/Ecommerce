<?php
namespace Magenest\CouponCode\Model\Configurations;

use Magento\SalesRule\Model\Data\Rule;

class DefaultApply extends AbstractFields
{
    /**
     * @var string
     */
    public const SALESRULE_TABLE = 'salesrule';
    public const SALESRULE_CUSTOMER_GROUP_TABLE = 'salesrule_customer_group';
    public const SALESRULE_COUPON_USAGE_TABLE = 'salesrule_coupon_usage';
    public const SALESRULE_COUPON_TABLE = 'salesrule_coupon';
    public const SALESRULE_WEBSITE_TABLE = 'salesrule_website';

    /**
     * @inheritdoc
     */
    public function apply($rules)
    {
        $customer = $this->getCustomer();
        $customerId = $customer->getId();
        $autoIncrementField = $rules->getConnection()->getAutoIncrementField(self::SALESRULE_TABLE);

        $rules->addFieldToFilter(Rule::KEY_COUPON_TYPE, \Magento\SalesRule\Model\Rule::COUPON_TYPE_SPECIFIC)
            ->addFieldToFilter(Rule::KEY_USE_AUTO_GENERATION, 0);
        $select = $rules->getSelect();

        $customerGroupTable = $this->getTableName(self::SALESRULE_CUSTOMER_GROUP_TABLE);
        $couponUsageTable = $this->getTableName(self::SALESRULE_COUPON_USAGE_TABLE);
        $couponTable = $this->getTableName(self::SALESRULE_COUPON_TABLE);
        $websiteTable = $this->getTableName(self::SALESRULE_WEBSITE_TABLE);

        /**
        rule_coupons name in \Magento\SalesRule\Model\ResourceModel\Rule\Collection $collection
         */
        $select->columns(['rule_coupons.coupon_id']);

        $select->join(
            ['customer_group' => $customerGroupTable],
            'main_table.'.$autoIncrementField.' = customer_group.'.$autoIncrementField,
            ['customer_group_id'
            => new \Zend_Db_Expr('group_concat(DISTINCT `customer_group`.customer_group_id SEPARATOR ",")')]
        )->group('main_table.rule_id');

        $select->joinLeft(
            ['coupon' => $couponTable],
            'rule_coupons.rule_id = coupon.rule_id',
            ['times_used_coupon' => 'coupon.times_used','usage_limit','usage_per_customer']
        );

        if ($customerId) {
            $select->joinLeft(
                ['coupon_usage' => $couponUsageTable],
                'rule_coupons.coupon_id = coupon_usage.coupon_id AND coupon_usage.customer_id=' . $customerId,
                ['times_used_customer' => 'coupon_usage.times_used', 'customer_id']
            );
        }

        $select->join(
            ['website_table' => $websiteTable],
            'main_table.'.$autoIncrementField.' = website_table.'.$autoIncrementField,
            ['website_id' => new \Zend_Db_Expr('group_concat(DISTINCT `website_table`.website_id SEPARATOR ",")')]
        );

        return $rules;
    }
}
