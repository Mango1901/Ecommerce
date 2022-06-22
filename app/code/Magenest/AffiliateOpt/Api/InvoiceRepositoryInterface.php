<?php

namespace Magenest\AffiliateOpt\Api;

use Magento\Framework\Exception\NoSuchEntityException;
use Magenest\AffiliateOpt\Api\Data\AffiliateInvoiceInterface;
use Magenest\AffiliateOpt\Api\Data\AffiliateInvoiceItemInterface;

/**
 * Interface InvoiceRepositoryInterface
 * @api
 */
interface InvoiceRepositoryInterface
{
    /**
     * @param int $invoiceId The Invoice ID.
     *
     * @return AffiliateInvoiceInterface Affiliate.
     * @throws NoSuchEntityException
     */
    public function get($invoiceId);

    /**
     * @param int $itemId The item ID.
     *
     * @return AffiliateInvoiceItemInterface Affiliate item.
     * @throws NoSuchEntityException
     */
    public function getItemById($itemId);
}
