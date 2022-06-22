<?php
namespace Magenest\CouponCode\Model\Configurations;

use Magento\SalesRule\Model\Data\Rule;

class Active extends AbstractFields
{
    public const CODE = "is_active";

    /**
     * @inheritdoc
     */
    public function apply($rules)
    {
        if ($this->getConfigurationFieldByCode(self::CODE)) {
            $rules->addFieldToFilter(Rule::KEY_IS_ACTIVE, $this->getConfigurationFieldByCode(self::CODE));
        }
        return $rules;
    }
}
