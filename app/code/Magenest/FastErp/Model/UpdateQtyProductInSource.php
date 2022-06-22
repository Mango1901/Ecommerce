<?php

namespace Magenest\FastErp\Model;

use Magenest\FastErp\Api\UpdateQtyProductInSourceInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Psr\Log\LoggerInterface;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class UpdateQtyProductInSource implements UpdateQtyProductInSourceInterface
{
    /**
     * @var ClientFactory
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var GetSourceItemsBySkuInterface
     */
    private $getSourceItemsBySku;

    /**
     * @var SourceItemsSaveInterface
     */
    private $sourceItemsSave;

    /**
     * @var SourceRepositoryInterface
     */
    private $sourceRepository;

    /**
     * @var SourceItemInterfaceFactory
     */
    private $sourceItemInterfaceFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private $criteriaBuilder;

    /**
     * @var SourceItemRepositoryInterface
     */
    private $sourceItemRepository;

    /**
     * @param ClientFactory $client
     * @param LoggerInterface $logger
     * @param GetSourceItemsBySkuInterface $getSourceItemsBySku
     * @param SourceItemsSaveInterface $sourceItemsSave
     * @param SourceRepositoryInterface $sourceRepository
     * @param SourceItemInterfaceFactory $sourceItemInterfaceFactory
     * @param SearchCriteriaBuilder $criteriaBuilder
     * @param SourceItemRepositoryInterface $sourceItemRepository
     */
    public function __construct(
        ClientFactory $client,
        LoggerInterface $logger,
        GetSourceItemsBySkuInterface $getSourceItemsBySku,
        SourceItemsSaveInterface $sourceItemsSave,
        SourceRepositoryInterface $sourceRepository,
        SourceItemInterfaceFactory $sourceItemInterfaceFactory,
        SearchCriteriaBuilder $criteriaBuilder,
        SourceItemRepositoryInterface $sourceItemRepository
    ) {
        $this->client = $client;
        $this->logger = $logger;
        $this->getSourceItemsBySku = $getSourceItemsBySku;
        $this->sourceItemsSave = $sourceItemsSave;
        $this->sourceRepository = $sourceRepository;
        $this->sourceItemInterfaceFactory = $sourceItemInterfaceFactory;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->sourceItemRepository = $sourceItemRepository;
    }

    /**
     * @inheirtDoc
     */
    function execute()
    {
        try {
            /**
             * @var $client Client
             */
            $client = $this->client->create();

            $sourceLists = $this->sourceRepository->getList();

            $erpSourceCodeArray = [];
            foreach ($sourceLists->getItems() as $source) {
                $erpSourceCodeArray[$source->getSourceCode()] = $source->getErpSourceCode();
            }

            $productInResponse = [];

            foreach ($erpSourceCodeArray as $sourceCode => $erpSourceCode) {
                $response = $client->getStock($erpSourceCode);
                foreach ($response as $erpSourceItem) {
                    $skuProductErp = $erpSourceItem['productId'];
                    $sourceItems = $this->getSourceItemsBySku->execute($skuProductErp);
                    $isProductExistsInSource = false;
                    foreach ($sourceItems as $sourceItem) {
                        // Case product on erp assigned source code in magento
                        if ($sourceItem->getSourceCode() === $sourceCode) {
                            $isProductExistsInSource = true;
                            $productInResponse[$sourceCode][] = $skuProductErp;
                            $sourceItem->setQuantity($erpSourceItem['quantity']);
                            $this->sourceItemsSave->execute([$sourceItem]);
                        }
                    }

                    // Case product on erp not assigned source code in magento
                    if (!$isProductExistsInSource) {
                        /**
                         * @var $sourceItemInterface SourceItemInterface
                         */
                        $sourceItemInterface = $this->sourceItemInterfaceFactory->create();
                        $sourceItemInterface->setQuantity($erpSourceItem['quantity']);
                        $sourceItemInterface->setSourceCode($sourceCode);
                        $sourceItemInterface->setSku($skuProductErp);
                        $sourceItemInterface->setStatus($erpSourceItem['quantity'] ? 1 : 0);
                        $this->sourceItemsSave->execute([$sourceItemInterface]);
                    }
                }
            }

            if (count($productInResponse)) {
                foreach ($productInResponse as $sourceCode => $skus) {
                    $this->updateSourcesItemsNotFromErp($sourceCode, $skus);
                }
            }

            return true;
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
        return false;
    }

    /**
     * Update qty of source item in system and not from erp
     *
     * @param $sourceCode
     * @param $skus
     * @return bool
     */
    private function updateSourcesItemsNotFromErp($sourceCode, $skus)
    {
        try {
            $searchCriteria = $this->criteriaBuilder
                ->addFilter('source_code', $sourceCode)
                ->addFilter('sku', array_unique($skus), 'nin')
                ->create();
            $sourceItemNotErps = $this->sourceItemRepository->getList($searchCriteria)->getItems();
            foreach ($sourceItemNotErps as &$sourceItem) {
                $sourceItem->setQuantity(0);
            }
            $this->sourceItemsSave->execute($sourceItemNotErps);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
            return false;
        }
        return true;
    }
}
