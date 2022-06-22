<?php
/**
 * Copyright © CustomInventoryReservation All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\CustomInventoryReservation\Plugin\Magento\InventoryIndexer\Model\ResourceModel\Frontend;

use Closure;
use Exception;
use Magenest\CustomSource\Helper\Data;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\State;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface;
use Magento\InventoryCatalogApi\Model\GetProductIdsBySkusInterface;
use Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface;
use Magento\InventoryIndexer\Indexer\IndexStructure;
use Magento\InventoryIndexer\Model\ResourceModel\GetStockItemData as GetStockItemDataMagento;
use Magento\InventoryIndexer\Model\StockIndexTableNameResolverInterface;
use Magento\InventorySalesApi\Model\GetStockItemDataInterface;
use Psr\Log\LoggerInterface;

class GetStockItemData
{
    /**
     * @var Data
     */
    private $dataHelper;
    /**
     * @var SourceRepositoryInterface
     */
    private $sourceRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var State
     */
    private $state;
    /**
     * @var DefaultStockProviderInterface
     */
    private $defaultStockProvider;

    /**
     * @var GetProductIdsBySkusInterface
     */
    private $getProductIdsBySkus;

    /**
     * @var StockIndexTableNameResolverInterface
     */
    private $stockIndexTableNameResolver;
    /**
     * @var GetProductTypesBySkusInterface
     */
    private $getProductTypeBySku;

    /**
     * @param Data $dataHelper
     * @param SourceRepositoryInterface $sourceRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param LoggerInterface $logger
     * @param State $state
     * @param DefaultStockProviderInterface $defaultStockProvider
     * @param GetProductIdsBySkusInterface $getProductIdsBySkus
     * @param StockIndexTableNameResolverInterface $stockIndexTableNameResolver
     * @param GetProductTypesBySkusInterface $getProductTypeBySku
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Data                                 $dataHelper,
        SourceRepositoryInterface            $sourceRepository,
        SearchCriteriaBuilder                $searchCriteriaBuilder,
        LoggerInterface                      $logger,
        State                                $state,
        DefaultStockProviderInterface        $defaultStockProvider,
        GetProductIdsBySkusInterface         $getProductIdsBySkus,
        StockIndexTableNameResolverInterface $stockIndexTableNameResolver,
        GetProductTypesBySkusInterface       $getProductTypeBySku,
        ResourceConnection                   $resourceConnection
    ) {
        $this->dataHelper = $dataHelper;
        $this->sourceRepository = $sourceRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->resourceConnection = $resourceConnection;
        $this->logger = $logger;
        $this->state = $state;
        $this->defaultStockProvider = $defaultStockProvider;
        $this->getProductIdsBySkus = $getProductIdsBySkus;
        $this->getProductTypeBySku = $getProductTypeBySku;
        $this->stockIndexTableNameResolver = $stockIndexTableNameResolver;
    }

    public function aroundExecute(
        GetStockItemDataMagento $subject,
        Closure                 $proceed,
        string                  $sku,
        int                     $stockId
    ) {
        $connection = $this->resourceConnection->getConnection();
        try {
            $areaCode = $this->dataHelper->getCurrentArea();
            if ($this->defaultStockProvider->getId() === $stockId) {
                $productId = current($this->getProductIdsBySkus->execute([$sku]));
                $select = $connection->select()->from(
                    ["main_table" => $this->resourceConnection->getTableName('cataloginventory_stock_status')],
                    [
                        'quantity' => 'SUM(isi.quantity)',
                        GetStockItemDataInterface::IS_SALABLE => 'stock_status',
                    ]
                )->join(
                    ['cpe' => $connection->getTableName('catalog_product_entity')],
                    '`main_table`.`product_id` = `cpe`.`entity_id`',
                    []
                )->join(
                    ['isi' => $connection->getTableName('inventory_source_item')],
                    '`cpe`.`sku` = `isi`.`sku`',
                    ["source_code"]
                )->join(
                    ['is' => $connection->getTableName('inventory_source')],
                    '`isi`.`source_code` = `is`.`source_code`',
                    ["area_code"]
                )->where(
                    'product_id = ?',
                    $productId
                )->where(
                    'isi.status = ?',
                    1
                )->where(
                    'stock_status = ?',
                    1
                )->where(
                    '`is`.area_code = ?',
                    $areaCode
                );
                $qty = $connection->fetchRow($select);
            } else {
                $productTypes = $this->getProductTypeBySku->execute([$sku]);
                if ($productTypes[$sku] !== "bundle") {
                    $select = $connection->select()->from(
                        ["main_table" => $this->stockIndexTableNameResolver->execute($stockId)],
                        [
                            GetStockItemDataInterface::QUANTITY => 'SUM(isi.quantity)',
                            GetStockItemDataInterface::IS_SALABLE => IndexStructure::IS_SALABLE,
                        ]
                    )->join(
                        ['isi' => $connection->getTableName('inventory_source_item')],
                        '`main_table`.`sku` = `isi`.`sku`',
                        ["source_code"]
                    )->join(
                        ['is' => $connection->getTableName('inventory_source')],
                        '`isi`.`source_code` = `is`.`source_code`',
                        ["area_code"]
                    )->where(
                        'isi.sku IN (?)',
                        $sku
                    )->where(
                        'isi.status = ?',
                        1
                    )->where(
                        '`is`.area_code = ?',
                        $areaCode
                    );
                } else {
                    $select = $connection->select()->from(
                        $this->stockIndexTableNameResolver->execute($stockId),
                        [
                            GetStockItemDataInterface::QUANTITY => IndexStructure::QUANTITY,
                            GetStockItemDataInterface::IS_SALABLE => IndexStructure::IS_SALABLE,
                        ]
                    )->where(
                        IndexStructure::SKU . ' = ?',
                        $sku
                    );
                }

                $qty = $connection->fetchRow($select);
            }

            return $qty;
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }
}
