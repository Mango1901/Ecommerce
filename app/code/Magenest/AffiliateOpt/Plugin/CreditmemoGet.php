<?php

namespace Magenest\AffiliateOpt\Plugin;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Api\Data\CreditmemoExtension;
use Magento\Sales\Api\Data\CreditmemoExtensionFactory;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\CreditmemoItemExtension;
use Magento\Sales\Api\Data\CreditmemoItemExtensionFactory;
use Magento\Sales\Api\Data\CreditmemoItemInterface;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection;
use Magenest\AffiliateOpt\Api\Data\AffiliateCreditmemoInterface;
use Magenest\AffiliateOpt\Api\Data\AffiliateCreditmemoItemInterface;

/**
 * Class CreditmemoGet
 *
 * @package Magenest\AffiliateOpt\Plugin
 */
class CreditmemoGet
{
    /**
     * @var \Magenest\AffiliateOpt\Api\CreditmemoRepositoryInterface
     */
    protected $creditmemoRepository;

    /**
     * @var CreditmemoExtensionFactory
     */
    protected $creditmemoExtensionFactory;

    /**
     * @var CreditmemoItemExtensionFactory
     */
    protected $creditmemoItemExtensionFactory;

    /**
     * CreditmemoGet constructor.
     *
     * @param \Magenest\AffiliateOpt\Api\CreditmemoRepositoryInterface $creditmemoRepository
     * @param CreditmemoExtensionFactory                                     $creditmemoExtensionFactory
     * @param CreditmemoItemExtensionFactory                                 $creditmemoItemExtensionFactory
     */
    public function __construct(
        \Magenest\AffiliateOpt\Api\CreditmemoRepositoryInterface $creditmemoRepository,
        CreditmemoExtensionFactory $creditmemoExtensionFactory,
        CreditmemoItemExtensionFactory $creditmemoItemExtensionFactory
    ) {
        $this->creditmemoRepository           = $creditmemoRepository;
        $this->creditmemoExtensionFactory     = $creditmemoExtensionFactory;
        $this->creditmemoItemExtensionFactory = $creditmemoItemExtensionFactory;
    }

    /**
     * @param CreditmemoRepositoryInterface $subject
     * @param CreditmemoInterface           $resultCreditmemo
     *
     * @return CreditmemoInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(
        CreditmemoRepositoryInterface $subject,
        CreditmemoInterface $resultCreditmemo
    ) {
        $resultCreditmemo = $this->getOrderAffiliate($resultCreditmemo);
        $resultCreditmemo = $this->getOrderItemAffiliate($resultCreditmemo);

        return $resultCreditmemo;
    }

    /**
     * @param CreditmemoInterface $creditmemo
     *
     * @return CreditmemoInterface
     */
    protected function getOrderAffiliate(CreditmemoInterface $creditmemo)
    {
        $extensionAttributes = $creditmemo->getExtensionAttributes();
        if ($extensionAttributes && $extensionAttributes->getMpAffiliate()) {
            return $creditmemo;
        }

        try {
            /** @var AffiliateCreditmemoInterface $affiliateData */
            $affiliateData = $this->creditmemoRepository->get($creditmemo->getEntityId());
        } catch (NoSuchEntityException $e) {
            return $creditmemo;
        }

        /** @var CreditmemoExtension $creditmemoExtension */
        $creditmemoExtension = $extensionAttributes
            ? $extensionAttributes
            : $this->creditmemoExtensionFactory->create();
        $creditmemoExtension->setMpAffiliate($affiliateData);
        $creditmemo->setExtensionAttributes($creditmemoExtension);

        return $creditmemo;
    }

    /**
     * @param CreditmemoInterface $creditmemo
     *
     * @return CreditmemoInterface
     */
    protected function getOrderItemAffiliate(CreditmemoInterface $creditmemo)
    {
        $creditmemoItems = $creditmemo->getItems();
        if (null !== $creditmemoItems) {
            /** @var CreditmemoItemInterface $creditmemoItem */
            foreach ($creditmemoItems as $creditmemoItem) {
                $extensionAttributes = $creditmemoItem->getExtensionAttributes();

                if ($extensionAttributes && $extensionAttributes->getMpAffiliate()) {
                    continue;
                }

                try {
                    /** @var AffiliateCreditmemoItemInterface $affiliateData */
                    $affiliateData = $this->creditmemoRepository->getItemById($creditmemoItem->getItemId());
                } catch (NoSuchEntityException $e) {
                    continue;
                }

                /** @var CreditmemoItemExtension $creditmemoItemExtension */
                $invoiceItemExtension = $extensionAttributes
                    ? $extensionAttributes
                    : $this->creditmemoItemExtensionFactory->create();
                $invoiceItemExtension->setMpAffiliate($affiliateData);
                $creditmemoItem->setExtensionAttributes($invoiceItemExtension);
            }
        }

        return $creditmemo;
    }

    /**
     * @param CreditmemoRepositoryInterface $subject
     * @param Collection                    $resultCreditmemo
     *
     * @return Collection
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetList(
        CreditmemoRepositoryInterface $subject,
        Collection $resultCreditmemo
    ) {
        /** @var  $creditmemo */
        foreach ($resultCreditmemo->getItems() as $creditmemo) {
            $this->afterGet($subject, $creditmemo);
        }

        return $resultCreditmemo;
    }
}
