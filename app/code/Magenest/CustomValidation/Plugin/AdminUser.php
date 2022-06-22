<?php
namespace Magenest\CustomValidation\Plugin;

use Magenest\VietnameseUrlKey\Model\VietnameseConverter;
use Magento\User\Model\User;

class AdminUser
{
    /** @var VietnameseConverter */
    private $vietnameseConverter;

    public function __construct(VietnameseConverter $vietnameseConverter)
    {
        return $this->vietnameseConverter = $vietnameseConverter;
    }

    /**
     * @param User $subject
     * @param $result
     *
     * @return array|bool
     */
    public function afterValidate(User $subject, $result)
    {
        if ($subject->getPassword() && $subject->getUserName() && $subject->getFirstName() && $subject->getLastName()) {
            $password  = strtolower($subject->getPassword());
            $username  = strtolower($subject->getUserName());
            $firstName = strtolower($this->vietnameseConverter->convert($subject->getFirstName()));
            $lastName  = strtolower($this->vietnameseConverter->convert($subject->getLastName()));

            if (strpos($password, $username) !== false
                || strpos($password, $firstName) !== false
                || strpos($password, $lastName) !== false) {
                $result = is_array($result)
                    ? array_merge($result, [__("This password contains user name. Please choose another one.")])
                    : [__("This password contains user name. Please choose another one.")];

            }
        }

        return $result;
    }
}
