<?php
namespace Magenest\CouponCode\Model\Configurations;

class ToDate extends AbstractFields
{
    public const CODE = "to_date";

    /**
     * @inheritdoc
     */
    public function apply($rules)
    {
        $currentDate = $this->getCurrentDayFromCE();
        if ($currentDate != '' && $this->getConfigurationFieldByCode(self::CODE)) {
            $rules->addFieldToFilter(
                ['to_date', 'to_date'],
                [
                    ['gteq' => $currentDate],
                    ['null' => true]
                ]
            );
        }
        return $rules;
    }
}
