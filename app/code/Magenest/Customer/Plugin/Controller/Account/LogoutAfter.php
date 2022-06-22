<?php

namespace Magenest\Customer\Plugin\Controller\Account;

use Magenest\Customer\Helper\ConfigHelper;
use Magento\Customer\Controller\Account\Logout;
use Magento\Framework\Controller\Result\Redirect;

class LogoutAfter
{
    /**
     * @var ConfigHelper
     */
    private $configHelper;

    /**
     * @param ConfigHelper $configHelper
     */
    public function __construct(ConfigHelper $configHelper)
    {
        $this->configHelper = $configHelper;
    }

    /**
     * @param Logout $subject
     * @param Redirect $result
     * @return Redirect
     */
    public function afterExecute(Logout $subject, $result)
    {
        if ($this->configHelper->isEnabledRedirectHomePageWhenLogout()) {
            $result->setPath('');
        }

        return $result;
    }
}
