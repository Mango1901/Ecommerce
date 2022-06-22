<?php

namespace Magenest\OrderExtraInformation\Helper;


use Magento\Framework\Exception\InputException;

class Validator
{
    /**
     * @throws InputException
     */
    public static function validateEmail($email)
    {
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InputException(
                __('Email has a wrong format.')
            );
        }
        return true;
    }
}
