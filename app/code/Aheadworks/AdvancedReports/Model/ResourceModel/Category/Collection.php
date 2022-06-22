<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\ResourceModel\Category;

use Magento\Framework\DataObject;
use Aheadworks\AdvancedReports\Model\ResourceModel\Category as ResourceCategory;

/**
 * Class Collection
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\Category
 */
class Collection extends \Aheadworks\AdvancedReports\Model\ResourceModel\AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init(DataObject::class, ResourceCategory::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        $this->getSelect()
            ->from(['main_table' => $this->getMainTable()], [])
            ->columns($this->getColumns(true))
            ->group('category_id');
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns($addRate = false)
    {
        $orderItemsCount = 'SUM(COALESCE(main_table.order_items_count, 0))';
        $rateField = $this->getRateField($addRate);
        return [
            'category' => 'category',
            'category_id' => 'category_id',
            'order_items_count' => $orderItemsCount,
            'subtotal' => 'SUM(COALESCE(main_table.subtotal' . $rateField . ', 0))',
            'tax' => 'SUM(COALESCE(main_table.tax' . $rateField . ', 0))',
            'discount' => 'SUM(COALESCE(main_table.discount' . $rateField . ', 0))',
            'total' => 'SUM(COALESCE(main_table.total' . $rateField . ', 0))',
            'invoiced' => 'SUM(COALESCE(main_table.invoiced' . $rateField . ', 0))',
            'refunded' => 'SUM(COALESCE(main_table.refunded' . $rateField . ', 0))',
            'avg_item_final_price' => '(IF(' . $orderItemsCount . ' > 0, 
                        SUM(COALESCE(main_table.subtotal' . $rateField . ', 0)) / ' . $orderItemsCount . ', 0.0))'
        ];
    }
}
