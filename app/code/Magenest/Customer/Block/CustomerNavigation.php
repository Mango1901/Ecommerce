<?php

namespace Magenest\Customer\Block;

/**
 * Class Navigation
 * @package Magenest\Customer\Block
 */
class CustomerNavigation extends AbstractNavigation
{
    /**
     * Search redundant /index and / in url
     */
    const REGEX_URL_PATTERN = '/customer\/address|vault|customer\/account/';

    /**
     * @inheritDoc
     */
    protected function additionalClass()
    {
        return "account-info";
    }
}
