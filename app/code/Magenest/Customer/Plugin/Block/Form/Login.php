<?php
namespace Magenest\Customer\Plugin\Block\Form;

use Magenest\Customer\Helper\ConfigHelper;

class Login
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
     * @param \Magento\Customer\Block\Form\Login $subject
     */
    public function beforeToHtml(\Magento\Customer\Block\Form\Login $subject)
    {
        if ($this->configHelper->isEnabledLoginWithTelephone()) {
            $subject->setTemplate('Magenest_Customer::form/login.phtml');
        }
    }
}
