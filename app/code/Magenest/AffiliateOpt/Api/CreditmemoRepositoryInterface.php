<?php

namespace Magenest\AffiliateOpt\Api;

use Magento\Framework\Exception\NoSuchEntityException;
use Magenest\AffiliateOpt\Api\Data\AffiliateCreditmemoInterface;
use Magenest\AffiliateOpt\Api\Data\AffiliateCreditmemoItemInterface;

/**
 * Interface CreditmemoRepositoryInterface
 * @api
 */
interface CreditmemoRepositoryInterface
{
    /**
     * @param int $creditmemoId The Creditmemo ID.
     *
     * @return AffiliateCreditmemoInterface Affiliate.
     * @throws NoSuchEntityException
     */
    public function get($creditmemoId);

    /**
     * @param int $itemId The item ID.
     *
     * @return AffiliateCreditmemoItemInterface Affiliate item.
     * @throws NoSuchEntityException
     */
    public function getItemById($itemId);
}
