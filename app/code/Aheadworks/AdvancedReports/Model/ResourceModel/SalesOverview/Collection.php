<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\ResourceModel\SalesOverview;

use Magento\Framework\DataObject;
use Aheadworks\AdvancedReports\Model\ResourceModel\SalesOverview as ResourceSalesOverview;

/**
 * Class Collection
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\SalesOverview
 */
class Collection extends \Aheadworks\AdvancedReports\Model\ResourceModel\AbstractCollection
{
    /**
     * Name of object id field
     *
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * @var string
     */
    protected $periodFilterForReport = 'addGroupByFilter';

    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init(DataObject::class, ResourceSalesOverview::class);
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
        $orderItemsCount = 'SUM(COALESCE(main_table.order_items_count, 0))';
        $orderCount = 'SUM(COALESCE(main_table.orders_count, 0))';
        $rateField = $this->getRateField($addRate);
        return [
            'orders_count' => $orderCount,
            'order_items_count' => $orderItemsCount,
            'subtotal' => 'SUM(COALESCE(main_table.subtotal' . $rateField . ', 0))',
            'tax' => 'SUM(COALESCE(main_table.tax' . $rateField . ', 0))',
            'shipping' => 'SUM(COALESCE(main_table.shipping' . $rateField . ', 0))',
            'discount' => 'SUM(COALESCE(main_table.discount' . $rateField . ', 0))',
            'total' => 'SUM(COALESCE(main_table.total' . $rateField . ', 0))',
            'invoiced' => 'SUM(COALESCE(main_table.invoiced' . $rateField . ', 0))',
            'refunded' => 'SUM(COALESCE(main_table.refunded' . $rateField . ', 0))',
            'avg_order_amount' => '(IF(' . $orderCount . ' > 0, SUM(COALESCE(main_table.total' . $rateField . ', 0)) / '
                . $orderCount . ', 0.0))',
            'avg_item_final_price' => '(IF(' . $orderItemsCount . ' > 0, 
                        SUM(COALESCE(main_table.subtotal' . $rateField . ', 0)) / ' . $orderItemsCount . ', 0.0))'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'coupon_code') {
            return $this->addCouponCodeFilter($condition);
        }
        if ($field == 'payment_type') {
            return $this->addPaymentCodeFilter($condition);
        }
        if ($field == 'periodFilter') {
            return $this->addGroupByFilter();
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add coupon code filter to collection
     *
     * @param [] $condition
     * @return $this
     */
    public function addCouponCodeFilter($condition)
    {
        $this->changeMainTable('_coupon_code');
        $this->conditionsForGroupBy[] = [
            'field' => 'main_table.coupon_code',
            'condition' => $condition
        ];
        return $this;
    }

    /**
     * Add payment code filter to collection
     *
     * @param [] $condition
     * @return $this
     */
    public function addPaymentCodeFilter($condition)
    {
        $this->conditionsForGroupBy[] = [
            'field' => 'main_table.payment_method',
            'condition' => $condition
        ];
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addStoreFilter()
    {
        $storeIds = $this->storeFilter->getStoreIds();
        if (null != $storeIds) {
            $this->conditionsForGroupBy[] = [
                'field' => 'main_table.store_id',
                'condition' => ['in' => $storeIds]
            ];
        }
        return $this;
    }
}
