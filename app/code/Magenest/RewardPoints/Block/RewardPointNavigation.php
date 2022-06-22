<?php

namespace Magenest\RewardPoints\Block;

use Magenest\Customer\Block\AbstractNavigation;

class RewardPointNavigation extends AbstractNavigation
{
    /**
     * Search redundant /index and / in url
     */
    const REGEX_URL_PATTERN = '/rewardpoints|voucher|storecredit/';

    /**
     * @inheritDoc
     */
    protected function additionalClass()
    {
        return "offers";
    }
}
