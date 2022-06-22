<?php
namespace Magenest\CouponCode\Model\Configurations;

use Magento\SalesRule\Model\Data\Rule;

class Website extends AbstractFields
{
    public const CODE = "website_id";

    /**
     * @inheritdoc
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function apply($rules)
    {
        $currentWebsite = $this->getCurrentWebsiteId();
        if ($this->getConfigurationFieldByCode(self::CODE)) {
            $rules->addFieldToFilter(Rule::KEY_WEBSITES, $currentWebsite);
        }

        return $rules;
    }
}
