<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * User: leo
 * Date: 21/06/2022
 * Time: 09:09
 */
declare(strict_types=1);

namespace Magenest\RewardPoints\Api;

/**
 * Interface GetReferralCodeByCustomerInterface
 */
interface GetReferralCodeByCustomerInterface
{
    /**
     * @param $customerId
     * @return string
     */
    public function execute($customerId);
}
