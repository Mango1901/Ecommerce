<?php

namespace Magenest\AffiliateOpt\Model;

use Magenest\AffiliateOpt\Api\InvoiceRepositoryInterface;
use Magenest\AffiliateOpt\Model\AffiliateInvoiceFactory;
use Magenest\AffiliateOpt\Model\AffiliateInvoiceItemFactory;

/**
 * Class InvoiceRepository
 * @package Magenest\AffiliateOpt\Model
 */
class InvoiceRepository implements InvoiceRepositoryInterface
{
    /**
     * @var \Magenest\AffiliateOpt\Model\AffiliateInvoiceFactory
     */
    protected $affiliateInvoiceFactory;

    /**
     * @var \Magenest\AffiliateOpt\Model\AffiliateInvoiceItemFactory
     */
    protected $affiliateInvoiceItemFactory;

    /**
     * InvoiceRepository constructor.
     *
     * @param \Magenest\AffiliateOpt\Model\AffiliateInvoiceFactory $affiliateInvoiceFactory
     * @param \Magenest\AffiliateOpt\Model\AffiliateInvoiceItemFactory $affiliateInvoiceItemFactory
     */
    public function __construct(
        AffiliateInvoiceFactory $affiliateInvoiceFactory,
        AffiliateInvoiceItemFactory $affiliateInvoiceItemFactory
    ) {
        $this->affiliateInvoiceFactory = $affiliateInvoiceFactory;
        $this->affiliateInvoiceItemFactory = $affiliateInvoiceItemFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function get($invoiceId)
    {
        return $this->affiliateInvoiceFactory->create()->load($invoiceId);
    }

    /**
     * {@inheritDoc}
     */
    public function getItemById($itemId)
    {
        return $this->affiliateInvoiceItemFactory->create()->load($itemId);
    }
}
