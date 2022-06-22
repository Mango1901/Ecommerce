<?php
/**
 * Copyright © CustomInventoryReservation All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\CustomInventoryReservation\Plugin\Magento\InventoryIndexer\Model\ResourceModel;

use Magenest\CustomSource\Helper\Data;
use Magenest\Directory\Block\Adminhtml\Area\Field\Area;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ResourceConnection;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface;
use Magento\InventoryCatalogApi\Model\GetProductIdsBySkusInterface;
use Magento\InventoryIndexer\Indexer\IndexStructure;
use Magento\InventoryIndexer\Model\StockIndexTableNameResolverInterface;
use Magento\InventorySalesApi\Model\GetStockItemDataInterface;
use \Psr\Log\LoggerInterface;
use \Magento\Framework\App\State;

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
     * @param Data $dataHelper
     * @param SourceRepositoryInterface $sourceRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param LoggerInterface $logger
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
     Data $dataHelper,
     SourceRepositoryInterface $sourceRepository,
     SearchCriteriaBuilder $searchCriteriaBuilder,
     LoggerInterface $logger,
     State $state,
     DefaultStockProviderInterface $defaultStockProvider,
     GetProductIdsBySkusInterface $getProductIdsBySkus,
     StockIndexTableNameResolverInterface $stockIndexTableNameResolver,
     ResourceConnection $resourceConnection
    ){
        $this->dataHelper = $dataHelper;
        $this->sourceRepository = $sourceRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->resourceConnection = $resourceConnection;
        $this->logger = $logger;
        $this->state = $state;
        $this->defaultStockProvider = $defaultStockProvider;
        $this->getProductIdsBySkus = $getProductIdsBySkus;
        $this->stockIndexTableNameResolver = $stockIndexTableNameResolver;
    }

    public function aroundExecute(
        \Magento\InventoryIndexer\Model\ResourceModel\GetStockItemData $subject,
        \Closure $proceed,
        string $sku,
        int $stockId
    ) {
        $connection = $this->resourceConnection->getConnection();
        $selectCatalogInventory = $connection->select();
        $selectInventoryStock = $connection->select();
        $selectInventoryStock->from(
            ["main_table" => $this->stockIndexTableNameResolver->execute($stockId)],
            [
                GetStockItemDataInterface::QUANTITY => 'isi.quantity',
                GetStockItemDataInterface::IS_SALABLE => IndexStructure::IS_SALABLE,
            ]
        )->join(
            ['isi' => $connection->getTableName('inventory_source_item')],
            '`main_table`.`sku` = `isi`.`sku`',
            ["source_code"]
        )->join(
            ['is'=> $connection->getTableName('inventory_source')],
            '`isi`.`source_code` = `is`.`source_code`',
            ["area_code"]
        );
        $selectCatalogInventory->from(
            ["main_table" => $this->resourceConnection->getTableName('cataloginventory_stock_status')],
            [
                'quantity' => 'isi.quantity',
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
            ['is'=> $connection->getTableName('inventory_source')],
            '`isi`.`source_code` = `is`.`source_code`',
            ["area_code"]
        );
        $sourceCodes = $this->dataHelper->getSourceCodeBySku($sku);

        if ($this->state->getAreaCode() == "adminhtml") {
            try {
                if ($this->defaultStockProvider->getId() === $stockId) {
                    $productId = current($this->getProductIdsBySkus->execute([$sku]));
                    $selectCatalogInventory->where(
                        'product_id = ?',
                        $productId
                    )->where(
                        'isi.status = ?',
                        1
                    )->where(
                        'stock_status = ?',
                        1
                    )->where(
                        'isi.source_code IN (?)',
                        $sourceCodes
                    );
                    $qty = $connection->fetchAll($selectCatalogInventory);
                } else {
                    $selectInventoryStock->where(
                        'isi.sku IN (?)',
                        $sku
                    )->where(
                        'isi.status = ?',
                        1
                    )->where(
                        'isi.source_code IN (?)',
                        $sourceCodes
                    );
                    $qty = $connection->fetchAll($selectInventoryStock);
                }

                return $qty;
            } catch (\Exception $exception) {
                $this->logger->error($exception->getMessage());
            }
        }
        if ($this->state->getAreaCode() == "frontend") {
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
                        ['is'=> $connection->getTableName('inventory_source')],
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
                    $qty = $connection->fetchRow($select);
                }

                return $qty;
            } catch (\Exception $exception) {
                $this->logger->error($exception->getMessage());
            }
        }

        $result = $proceed($sku, $stockId);
        return $result;
    }
}
