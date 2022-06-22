<?php
/**
 * Magenest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magenest.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magenest.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magenest
 * @package     Magenest_StoreCredit
 * @copyright   Copyright (c) Magenest (https://www.magenest.com/)
 * @license     https://www.magenest.com/LICENSE.txt
 */

namespace Magenest\StoreCredit\Plugin\Api;

use Exception;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Api\Data\CreditmemoExtension;
use Magento\Sales\Api\Data\CreditmemoExtensionFactory;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection;
use Magenest\StoreCredit\Api\Data\CreditmemoInterface as StoreCreditCreditmemoInterface;
use Magenest\StoreCredit\Model\CreditmemoFactory;

/**
 * Class CreditmemoGet
 * @package Magenest\StoreCredit\Plugin\Api
 */
class CreditmemoGet
{
    /**
     * @var CreditmemoFactory
     */
    protected $creditmemoFactory;

    /**
     * @var CreditmemoExtensionFactory
     */
    protected $creditmemoExtensionFactory;

    /**
     * CreditmemoGet constructor.
     *
     * @param CreditmemoFactory $creditmemoFactory
     * @param CreditmemoExtensionFactory $creditmemoExtensionFactory
     */
    public function __construct(
        CreditmemoFactory $creditmemoFactory,
        CreditmemoExtensionFactory $creditmemoExtensionFactory
    ) {
        $this->creditmemoFactory = $creditmemoFactory;
        $this->creditmemoExtensionFactory = $creditmemoExtensionFactory;
    }

    /**
     * @param CreditmemoRepositoryInterface $subject
     * @param CreditmemoInterface $resultCreditmemo
     *
     * @return CreditmemoInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(
        CreditmemoRepositoryInterface $subject,
        CreditmemoInterface $resultCreditmemo
    ) {
        $resultCreditmemo = $this->getCreditmemoStoreCredit($resultCreditmemo);

        return $resultCreditmemo;
    }

    /**
     * @param CreditmemoInterface $creditmemo
     *
     * @return CreditmemoInterface
     */
    protected function getCreditmemoStoreCredit(CreditmemoInterface $creditmemo)
    {
        $extensionAttributes = $creditmemo->getExtensionAttributes();
        if ($extensionAttributes && $extensionAttributes->getMpStoreCredit()) {
            return $creditmemo;
        }

        try {
            /** @var StoreCreditCreditmemoInterface $storeCreditData */
            $storeCreditData = $this->creditmemoFactory->create()->load($creditmemo->getEntityId());
        } catch (Exception $e) {
            return $creditmemo;
        }

        /** @var CreditmemoExtension $creditmemoExtension */
        $creditmemoExtension = $extensionAttributes ? $extensionAttributes : $this->creditmemoExtensionFactory->create();
        $creditmemoExtension->setMpStoreCredit($storeCreditData);
        $creditmemo->setExtensionAttributes($creditmemoExtension);

        return $creditmemo;
    }

    /**
     * @param CreditmemoRepositoryInterface $subject
     * @param Collection $resultCreditmemo
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
