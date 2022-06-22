<?php

namespace Magenest\Customer\Block;

class NotificationNavigation extends AbstractNavigation
{
    const REGEX_URL_PATTERN = '/notibox/';

    /**
     * @inheritDoc
     */
    protected function additionalClass()
    {
        return 'notification';
    }
}
