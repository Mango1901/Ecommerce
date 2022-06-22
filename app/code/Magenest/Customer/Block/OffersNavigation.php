<?php

namespace Magenest\Customer\Block;

class OffersNavigation extends AbstractNavigation
{
    /**
     * Search redundant /index and / in url
     */
    const REGEX_URL_PATTERN = '/offers/';

    /**
     * @inheritDoc
     */
    protected function additionalClass()
    {
        return "offers";
    }
}
