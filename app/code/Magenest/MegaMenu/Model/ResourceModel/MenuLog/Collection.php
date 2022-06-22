<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_SmartNet extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_SmartNet
 */

namespace Magenest\MegaMenu\Model\ResourceModel\MenuLog;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'log_id';

    /**
     * Initialize resource collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magenest\MegaMenu\Model\MenuLog::class, \Magenest\MegaMenu\Model\ResourceModel\MenuLog::class);
    }

    public function addCurrentVersionFilter($menuId)
    {
        $currentVersion = $this->getMenuVersion($menuId);
        if ($currentVersion) {
            $this->addFieldToFilter('version', ['neq' => $currentVersion]);
        }

        return $this;
    }

    protected function getMenuVersion($menuId)
    {
        $connection = $this->getResource()->getConnection();
        $megaMenuTable = $connection->getTableName('magenest_mega_menu');
        $select = $connection->select()->from($megaMenuTable, ['current_version']);
        $select->where('menu_id = ?', $menuId);

        return $connection->fetchOne($select);
    }
}
