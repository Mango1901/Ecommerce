<?php

namespace Magenest\Customer\Model;

use Magenest\Customer\Helper\ConfigHelper;
use Magento\Checkout\Model\ConfigProviderInterface;

class CustomConfigProvider implements ConfigProviderInterface
{
    private $configHelper;

    public function __construct(
        ConfigHelper $configHelper
    ) {
        $this->configHelper = $configHelper;
    }

    public function getConfig()
    {
        $config = [];
        $config['isEnabledLoginWithTelephone'] = $this->configHelper->isEnabledLoginWithTelephone();
        $config['isEnabledFullNameInstead'] = $this->configHelper->isEnabledFullNameInstead();

        return $config;
    }
}
