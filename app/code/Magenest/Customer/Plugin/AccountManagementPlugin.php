<?php

namespace Magenest\Customer\Plugin;

use Magenest\Customer\Helper\ConfigHelper;
use Magenest\Customer\Helper\Login;
use Magenest\Customer\Model\LoginByTelephone;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\AccountManagement;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\UserLockedException;

class AccountManagementPlugin
{
    const EMAIL_TYPE     = "email";
    const USERNAME_TYPE  = "username";
    const TELEPHONE_TYPE = "telephone";
    const INVALID        = "invalid";

    /**
     * @var LoginByTelephone
     */
    protected $loginByTelephone;

    /**
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * AccountManagementPlugin constructor.
     *
     * @param LoginByTelephone $loginByTelephone
     * @param ConfigHelper $configHelper
     */
    public function __construct(
        LoginByTelephone $loginByTelephone,
        ConfigHelper     $configHelper
    ) {
        $this->loginByTelephone = $loginByTelephone;
        $this->configHelper     = $configHelper;
    }

    /**
     * Fetch customer mail by telephone
     *
     * @param $subject
     * @param $username
     * @param $password
     *
     * @return array
     * @throws InvalidEmailOrPasswordException
     */
    public function beforeAuthenticate($subject, $username, $password)
    {
        if (!$this->configHelper->isEnabledLoginWithTelephone()) {
            return [$username, $password];
        }

        $type = $this->checkUsernameType($username);

        switch ($type) {
            case self::EMAIL_TYPE:
                break;
            case self::TELEPHONE_TYPE:
                if ($email = $this->loginByTelephone->authenticateByTelephone($username)) {
                    $username = $email;
                } else {
                    $this->returnError();
                }
                break;
            default:
                $this->returnError();
        }

        return [$username, $password];
    }

    /**
     * Function wrapper to output specific error messages
     * @return CustomerInterface
     * @throws LocalizedException
     */
    public function aroundAuthenticate(AccountManagement $subject, callable $proceed, ...$args)
    {
        try {
            return $proceed(...$args);
        } catch (InvalidEmailOrPasswordException $e) {
            throw new LocalizedException(__('The account sign-in was incorrect. Please wait and try again later.'));
        } catch (UserLockedException $e) {
            throw new LocalizedException(__('Your account is disabled temporarily. Please wait and try again later.'));
        }
    }

    /**
     * @param $username
     *
     * @return string
     */
    private function checkUsernameType($username)
    {
        if (preg_match(Login::REGEX_EMAIL, $username)) {
            return self::EMAIL_TYPE;
        } elseif (preg_match(Login::REGEX_MOBILE_NUMBER, $username)) {
            return self::TELEPHONE_TYPE;
        }

        return self::INVALID;
    }

    /**
     * @throws InvalidEmailOrPasswordException
     */
    protected function returnError()
    {
        throw new InvalidEmailOrPasswordException(__('Invalid login or password.'));
    }
}
