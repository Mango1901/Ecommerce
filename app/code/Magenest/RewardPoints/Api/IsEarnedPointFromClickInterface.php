<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * User: leo
 * Date: 21/06/2022
 * Time: 11:56
 */
declare(strict_types=1);

namespace Magenest\RewardPoints\Api;

interface IsEarnedPointFromClickInterface
{
    /**
     * @return mixed
     */
    public function execute($customerId, $applyCustomerId);
}
