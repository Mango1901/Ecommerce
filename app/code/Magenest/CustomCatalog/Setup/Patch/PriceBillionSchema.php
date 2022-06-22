<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Kangaroo extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Kangaroo
 */

namespace Magenest\CustomCatalog\Setup\Patch;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Api\SimpleDataObjectConverter;

abstract class PriceBillionSchema implements \Magento\Framework\Setup\Patch\SchemaPatchInterface
{
    protected $setup;

    /**
     * UpdateProductPriceLimit constructor.
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $schemaSetup
     */
    public function __construct(
        \Magento\Framework\Setup\SchemaSetupInterface $schemaSetup
    ) {
        $this->setup = $schemaSetup;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    abstract public function apply();

    protected function updateTableColumns($tableName, $columns, $null = true)
    {
        if ($this->setup->tableExists($tableName)) {
            foreach ($columns as $column) {
                $comment = SimpleDataObjectConverter::snakeCaseToCamelCase($column);
                $this->setup->getConnection()->modifyColumn(
                    $this->setup->getTable($tableName), $column, [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => "15,4",
                    'nullable' => $null,
                    'comment' => $comment
                ]);
            }
        }
    }
}
