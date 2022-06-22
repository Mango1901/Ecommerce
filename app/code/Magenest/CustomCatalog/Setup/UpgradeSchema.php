<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\CustomCatalog\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * UpgradeSchema
 * @package Magenest\CustomCatalog\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            $setup->getConnection()->modifyColumn($setup->getTable('catalog_product_entity_tier_price'), 'percentage_value', ['type' => Table::TYPE_DECIMAL, 'length' => '12,6', 'nullable' => true]);
        }
        if (version_compare($context->getVersion(), '1.0.4') < 0) {
            /* UPDATE catalog_product_entity_tier_price SET percentage_value=null WHERE percentage_value=0.00000000 */
            $bind['percentage_value'] = null;
            $where = ['percentage_value = ?' => 0.00000000];
            $setup->getConnection()->update(
                $setup->getTable('catalog_product_entity_tier_price'),
                $bind,
                $where
            );
        }
        $setup->endSetup();
    }
}
