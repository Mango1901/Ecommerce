<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\CustomCatalogSearch\CatalogSearch\Model\Indexer;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\CatalogSearch\Model\Indexer\IndexStructure as CoreIndexStructure;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Search\Request\Dimension;
use Magento\Framework\Indexer\IndexStructureInterface;
use Magento\Framework\Indexer\ScopeResolver\IndexScopeResolver;
use Magento\Framework\Search\Request\IndexScopeResolverInterface;

/**
 * @api
 * @since 100.0.2
 */
class IndexStructure extends CoreIndexStructure
{

    /**
     * @var Resource
     */
    private $resource;

    /**
     * @param ResourceConnection $resource
     * @param IndexScopeResolverInterface $indexScopeResolver
     */
    public function __construct(ResourceConnection $resource, IndexScopeResolverInterface $indexScopeResolver)
    {
        $this->resource = $resource;
        parent::__construct($resource, $indexScopeResolver);
    }

    /**
     * {@inheritdoc}
     */
    protected function createFulltextIndex($tableName)
    {
        $table = $this->resource->getConnection()->newTable($tableName)
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => false],
                'Entity ID'
            )->addColumn(
                'attribute_id',
                Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => false]
            )->addColumn(
                'data_index',
                Table::TYPE_TEXT,
                '4g',
                ['nullable' => true],
                'Data index'
            )->addColumn(
                'data_index_with_accent',
                Table::TYPE_TEXT,
                '4g',
                ['nullable' => true],
                'Data index with accent'
            )->addIndex(
                'idx_primary',
                ['entity_id', 'attribute_id'],
                ['type' => AdapterInterface::INDEX_TYPE_PRIMARY]
            )->addIndex(
                'FTI_FULLTEXT_DATA_INDEX',
                ['data_index'],
                ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
            );

        $this->resource->getConnection()->createTable($table);
        $this->resource->getConnection()->query("ALTER TABLE `".$this->resource->getTableName($tableName)."` CHANGE `data_index_with_accent` `data_index_with_accent` LONGTEXT CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL COMMENT 'Data index with accent'");
        $this->resource->getConnection()->addIndex(
            $this->resource->getTableName($tableName),
            'FTI_FULLTEXT_DATA_INDEX_WITH_ACCENT',
            ['data_index_with_accent'],
            AdapterInterface::INDEX_TYPE_FULLTEXT
        );
    }
}
