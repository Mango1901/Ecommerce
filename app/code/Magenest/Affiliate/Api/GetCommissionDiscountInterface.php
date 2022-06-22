<?php

namespace Magenest\Affiliate\Api;

/**
 * Class GetCommissionDiscountInterface
 *
 * Get Commission Discount Item
 */
interface GetCommissionDiscountInterface
{
    /**
     * @param $affiliateAccountId
     * @param $campaignId
     * @return array
     */
    public function execute($affiliateAccountId, $campaignId);
}
