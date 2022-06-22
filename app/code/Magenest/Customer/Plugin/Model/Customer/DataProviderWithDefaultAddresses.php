<?php

namespace Magenest\Customer\Plugin\Model\Customer;

use Magenest\Customer\Helper\ConfigHelper;

class DataProviderWithDefaultAddresses
{
    const NOT_VISIBLE_ATTRIBUTES = ['firstname', 'middlename', 'prefix', 'suffix', 'lastname'];

    /**
     * @var ConfigHelper
     */
    private $configHelper;
    public function __construct(
        ConfigHelper $configHelper
    ) {
        $this->configHelper = $configHelper;
    }

    /**
     * @param \Magento\Customer\Model\Customer\DataProviderWithDefaultAddresses $subject
     * @param array $result
     * @return array
     */
    public function afterGetMeta(\Magento\Customer\Model\Customer\DataProviderWithDefaultAddresses $subject, array $result)
    {
        if ($this->configHelper->isEnabledFullNameInstead()) {
            foreach (self::NOT_VISIBLE_ATTRIBUTES as $attribute) {
                if (isset($result['customer']['children'][$attribute]['arguments']['data']['config'])) {
                    $result['customer']['children'][$attribute]['arguments']['data']['config']['visible'] = false;
                }
            }
            if (isset($result['customer']['children']['fullname'])) {
                $result['customer']['children']['fullname']['arguments']['data']['config']['required'] = true;
                $result['customer']['children']['fullname']['arguments']['data']['config']['validation'] = [
                    'required-entry' => true
                ];
            }
        }
        return $result;
    }
}
