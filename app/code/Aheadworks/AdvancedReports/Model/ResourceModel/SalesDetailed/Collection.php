<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\ResourceModel\SalesDetailed;

use Magento\Framework\DataObject;
use Aheadworks\AdvancedReports\Model\ResourceModel\SalesDetailed as ResourceSalesDetailed;

/**
 * Class Collection
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\SalesDetailed
 */
class Collection extends \Aheadworks\AdvancedReports\Model\ResourceModel\AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init(DataObject::class, ResourceSalesDetailed::class);
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'include_refunded_items') {
            if ((int)$condition['eq'] == 1) {
                return $this;
            } else {
                return $this->addExcludeRefundedItemsFilter();
            }
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        $this->getSelect()
            ->from(['main_table' => $this->getMainTable()], [])
            ->columns($this->getColumns(true));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns($addRate = false)
    {
        return array_merge($this->getCounterColumns($addRate), $this->getOtherTypeColumns());
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function getTotalColumns($addRate = false)
    {
        $counterColumns = $this->getCounterColumns($addRate);
        foreach ($counterColumns as $alias => &$item) {
            $item = 'SUM(' . $item . ')';
        }
        return $counterColumns;
    }

    /**
     * Retrieve counter columns
     *
     * @param boolean $addRate
     * @return []
     */
    private function getCounterColumns($addRate)
    {
        $rateField = $this->getRateField($addRate);
        return [
            'qty_ordered' => 'qty_ordered',
            'qty_invoiced' => 'qty_invoiced',
            'qty_shipped' => 'qty_shipped',
            'qty_refunded' => 'qty_refunded',
            'item_price' => 'COALESCE(main_table.item_price' . $rateField . ', 0)',
            'item_cost' => 'COALESCE(main_table.item_cost' . $rateField . ', 0)',
            'subtotal' => 'COALESCE(main_table.subtotal' . $rateField . ', 0)',
            'discount' => 'COALESCE(main_table.discount' . $rateField . ', 0)',
            'tax' => 'COALESCE(main_table.tax' . $rateField . ', 0)',
            'total' => 'COALESCE(main_table.total' . $rateField . ', 0)',
            'total_incl_tax' => 'COALESCE(main_table.total_incl_tax' . $rateField . ', 0)',
            'invoiced' => 'COALESCE(main_table.invoiced' . $rateField . ', 0)',
            'tax_invoiced' => 'COALESCE(main_table.tax_invoiced' . $rateField . ', 0)',
            'invoiced_incl_tax' => 'COALESCE(main_table.invoiced_incl_tax' . $rateField . ', 0)',
            'refunded' => 'COALESCE(main_table.refunded' . $rateField . ', 0)',
            'tax_refunded' => 'COALESCE(main_table.tax_refunded' . $rateField . ', 0)',
            'refunded_incl_tax' => 'COALESCE(main_table.refunded_incl_tax' . $rateField . ', 0)'
        ];
    }

    /**
     * Retrieve other type columns
     *
     * @return []
     */
    private function getOtherTypeColumns()
    {
        return [
            'order_status',
            'order_date',
            'order_id',
            'order_increment_id',
            'product_id',
            'product_name',
            'sku',
            'manufacturer',
            'customer_id',
            'customer_email',
            'customer_name',
            'customer_group',
            'country',
            'region',
            'city',
            'zip_code',
            'address',
            'phone',
            'coupon_code'
        ];
    }

    /**
     * Add exclude refunded items filters to collection
     *
     * @return $this
     */
    private function addExcludeRefundedItemsFilter()
    {
        $this->getSelect()
            ->where('? > 0', new \Zend_Db_Expr('(qty_ordered - qty_refunded)'));
        return $this;
    }
}
