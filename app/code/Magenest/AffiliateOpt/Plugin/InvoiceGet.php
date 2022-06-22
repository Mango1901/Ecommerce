<?php

namespace Magenest\AffiliateOpt\Plugin;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\InvoiceExtension;
use Magento\Sales\Api\Data\InvoiceExtensionFactory;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\InvoiceItemExtension;
use Magento\Sales\Api\Data\InvoiceItemExtensionFactory;
use Magento\Sales\Api\Data\InvoiceItemInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection;
use Magenest\AffiliateOpt\Api\Data\AffiliateInvoiceInterface;
use Magenest\AffiliateOpt\Api\Data\AffiliateInvoiceItemInterface;

/**
 * Class InvoiceGet
 * @package Magenest\AffiliateOpt\Plugin
 */
class InvoiceGet
{
    /**
     * @var \Magenest\AffiliateOpt\Api\InvoiceRepositoryInterface
     */
    protected $invoiceRepository;

    /**
     * @var InvoiceExtensionFactory
     */
    protected $invoiceExtensionFactory;

    /**
     * @var InvoiceItemExtensionFactory
     */
    protected $invoiceItemExtensionFactory;

    /**
     * InvoiceGet constructor.
     *
     * @param \Magenest\AffiliateOpt\Api\InvoiceRepositoryInterface $invoiceRepository
     * @param InvoiceExtensionFactory $invoiceExtensionFactory
     * @param InvoiceItemExtensionFactory $invoiceItemExtensionFactory
     */
    public function __construct(
        \Magenest\AffiliateOpt\Api\InvoiceRepositoryInterface $invoiceRepository,
        InvoiceExtensionFactory $invoiceExtensionFactory,
        InvoiceItemExtensionFactory $invoiceItemExtensionFactory
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->invoiceExtensionFactory = $invoiceExtensionFactory;
        $this->invoiceItemExtensionFactory = $invoiceItemExtensionFactory;
    }

    /**
     * @param InvoiceRepositoryInterface $subject
     * @param InvoiceInterface $resultInvoice
     *
     * @return InvoiceInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(
        InvoiceRepositoryInterface $subject,
        InvoiceInterface $resultInvoice
    ) {
        $resultInvoice = $this->getOrderAffiliate($resultInvoice);
        $resultInvoice = $this->getOrderItemAffiliate($resultInvoice);

        return $resultInvoice;
    }

    /**
     * @param InvoiceInterface $invoice
     *
     * @return InvoiceInterface
     */
    protected function getOrderAffiliate(InvoiceInterface $invoice)
    {
        $extensionAttributes = $invoice->getExtensionAttributes();
        if ($extensionAttributes && $extensionAttributes->getMpAffiliate()) {
            return $invoice;
        }

        try {
            /** @var AffiliateInvoiceInterface $affiliateData */
            $affiliateData = $this->invoiceRepository->get($invoice->getEntityId());
        } catch (NoSuchEntityException $e) {
            return $invoice;
        }

        /** @var InvoiceExtension $invoiceExtension */
        $invoiceExtension = $extensionAttributes ? $extensionAttributes : $this->invoiceExtensionFactory->create();
        $invoiceExtension->setMpAffiliate($affiliateData);
        $invoice->setExtensionAttributes($invoiceExtension);

        return $invoice;
    }

    /**
     * @param InvoiceInterface $invoice
     *
     * @return InvoiceInterface
     */
    protected function getOrderItemAffiliate(InvoiceInterface $invoice)
    {
        $invoiceItems = $invoice->getItems();
        if (null !== $invoiceItems) {
            /** @var InvoiceItemInterface $invoiceItem */
            foreach ($invoiceItems as $invoiceItem) {
                $extensionAttributes = $invoiceItem->getExtensionAttributes();

                if ($extensionAttributes && $extensionAttributes->getMpAffiliate()) {
                    continue;
                }

                try {
                    /** @var AffiliateInvoiceItemInterface $affiliateData */
                    $affiliateData = $this->invoiceRepository->getItemById($invoiceItem->getItemId());
                } catch (NoSuchEntityException $e) {
                    continue;
                }

                /** @var InvoiceItemExtension $invoiceItemExtension */
                $invoiceItemExtension = $extensionAttributes
                    ? $extensionAttributes
                    : $this->invoiceItemExtensionFactory->create();
                $invoiceItemExtension->setMpAffiliate($affiliateData);
                $invoiceItem->setExtensionAttributes($invoiceItemExtension);
            }
        }

        return $invoice;
    }

    /**
     * @param InvoiceRepositoryInterface $subject
     * @param Collection $resultInvoice
     *
     * @return Collection
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetList(
        InvoiceRepositoryInterface $subject,
        Collection $resultInvoice
    ) {
        /** @var  $invoice */
        foreach ($resultInvoice->getItems() as $invoice) {
            $this->afterGet($subject, $invoice);
        }

        return $resultInvoice;
    }
}
