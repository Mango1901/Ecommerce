<?php

namespace Magenest\Customer\Plugin\Model\Address;

use Magenest\Customer\Helper\ConfigHelper;
use Magenest\Customer\Plugin\Model\Customer\DataProviderWithDefaultAddresses;

class DataProvider
{
    /**
     * @var ConfigHelper
     */
    private $configHelper;

    /**
     * @param ConfigHelper $configHelper
     */
    public function __construct(
        ConfigHelper $configHelper
    ) {
        $this->configHelper = $configHelper;
    }

    /**
     * @param \Magento\Customer\Model\Address\DataProvider $subject
     * @param array $result
     * @return array
     */
    public function afterGetMeta(\Magento\Customer\Model\Address\DataProvider $subject, array $result): array
    {
        if ($this->configHelper->isEnabledFullNameInstead()) {
            foreach (DataProviderWithDefaultAddresses::NOT_VISIBLE_ATTRIBUTES as $attribute) {
                if (isset($result['general']['children'][$attribute]['arguments']['data']['config'])) {
                    $result['general']['children'][$attribute]['arguments']['data']['config']['visible'] = false;
                }
            }
            if (isset($result['general']['children']['fullname'])) {
                $result['general']['children']['fullname']['arguments']['data']['config']['required'] = true;
                $result['general']['children']['fullname']['arguments']['data']['config']['validation'] = [
                    'required-entry' => true
                ];
            }
        }

        return $result;
    }
}
