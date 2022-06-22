<?php

namespace Magenest\AffiliateOpt\Api;

use Magento\Framework\Exception\NoSuchEntityException;
use Magenest\AffiliateOpt\Api\Data\AffiliateInterface;
use Magenest\AffiliateOpt\Api\Data\AffiliateItemInterface;

/**
 * Interface OrderRepositoryInterface
 * @api
 */
interface OrderRepositoryInterface
{
    /**
     * @param int $orderId The order ID.
     *
     * @return AffiliateInterface Affiliate.
     * @throws NoSuchEntityException
     */
    public function get($orderId);

    /**
     * @param int $itemId The item ID.
     *
     * @return AffiliateItemInterface Affiliate item.
     * @throws NoSuchEntityException
     */
    public function getItemById($itemId);
}
