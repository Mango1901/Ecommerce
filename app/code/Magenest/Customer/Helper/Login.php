<?php

namespace Magenest\Customer\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Login
 *
 * @package Magenest\AdvancedLogin\Helper
 */
class Login extends AbstractHelper
{
    const REGEX_MOBILE_NUMBER = '/^[0-9]{9,10}$/';

    const REGEX_EMAIL = '/^[_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i';

    /**
     * @param string $phone
     * @return string
     */
    public static function modifyMobileNumber($phone)
    {
        return preg_replace('/^(\+84|84)/', '0', $phone);
    }
}
