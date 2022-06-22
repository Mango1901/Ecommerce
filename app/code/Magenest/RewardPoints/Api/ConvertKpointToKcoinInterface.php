<?php

namespace Magenest\RewardPoints\Api;

interface ConvertKpointToKcoinInterface
{
    /**
     * @param int $customerId
     * @param int $kpoint
     * @return bool
     */
    public function execute($customerId, $kpoint);
}
