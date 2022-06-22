<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/11/16
 * Time: 09:59
 */

namespace Magenest\MapList\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magenest\MapList\Helper\Constant;

class InstallData implements InstallDataInterface
{
    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

//        $tableName = $installer->getTable(Constant::CATEGORY_TABLE);
//        if ($installer->getConnection()->isTableExists($tableName)) {
//            $data = [
//                [
//                    'category_id' => 1,
//                    'title' => 'Default category',
//                    'description' => 'Default category',
//                ],
//            ];
//
//            $installer->getConnection()->insertMultiple($tableName, $data);
//        }
//
//        $tableName = $installer->getTable(Constant::MAP_TABLE);
//        if ($installer->getConnection()->isTableExists($tableName)) {
//            $data = [
//                [
//                    'map_id' => 1,
//                    'title' => 'Default map',
//                    'description' => 'Default map',
//                ],
//            ];
//
//            $installer->getConnection()->insertMultiple($tableName, $data);
//        }
    }
}
