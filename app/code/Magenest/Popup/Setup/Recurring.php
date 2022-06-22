<?php
namespace Magenest\Popup\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class Recurring implements InstallSchemaInterface {
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $this->createTableDataLog($installer);
        $installer->endSetup();
    }
    public function createTableDataLog($installer){
        $tableName = $installer->getTable('magenest_log');
        if ($installer->getConnection()->isTableExists($tableName) !== true) {
            $magenest_log = $installer->getConnection()->newTable(
                $installer->getTable('magenest_log')
            )->addColumn(
                'log_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,[
                'unsigned' => true,
                'identity' => true,
                'nullable' => false,
                'primary' => true
            ],
                'Log Id'
            )->addColumn(
                'popup_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Popup Id'
            )->addColumn(
                'content',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'Data collect from frontend'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,[
                'nullable' => false,
                'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT
            ],
                'Created At'
            )->setComment('Tabel saved data collect from fronend');
            $installer->getConnection()->createTable($magenest_log);
        }

    }
}