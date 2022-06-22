<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\ResourceModel\ProductVariantPerformance;

use Magento\Framework\DataObject;
use Aheadworks\AdvancedReports\Model\ResourceModel\ProductVariantPerformance as ResourceProductVariantPerformance;
use Magento\Framework\DB\Select;

/**
 * Class Collection
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\ProductVariantPerformance
 */
class Collection extends \Aheadworks\AdvancedReports\Model\ResourceModel\AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init(DataObject::class, ResourceProductVariantPerformance::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        $this->getSelect()
            ->from(['main_table' => $this->getMainTable()], [])
            ->columns($this->getColumns(true))
            ->group(['product_id', 'sku']);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns($addRate = false)
    {
        $rateField = $this->getRateField($addRate);
        return [
                'product_id' => 'product_id',
                'product_name' => 'product_name',
                'sku' => 'sku',
                'order_items_count' => 'SUM(COALESCE(main_table.order_items_count, 0))',
                'subtotal' => 'SUM(COALESCE(main_table.subtotal' . $rateField . ', 0))',
                'tax' => 'SUM(COALESCE(main_table.tax' . $rateField . ', 0))',
                'discount' => 'SUM(COALESCE(main_table.discount' . $rateField . ', 0))',
                'total' => 'SUM(COALESCE(main_table.total' . $rateField . ', 0))',
                'invoiced' => 'SUM(COALESCE(main_table.invoiced' . $rateField . ', 0))',
                'refunded' => 'SUM(COALESCE(main_table.refunded' . $rateField . ', 0))'
            ];
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'sku') {
            return $this->addSkuFilter($condition);
        }
        if ($field == 'product_name') {
            return $this->addProductNameFilter($condition);
        }
        if ($field == 'product_id') {
            $condition = $condition['eq'];
            return $this->addProductFilter($condition['product_id'], $condition['parent_id']);
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add product id filter to collection
     *
     * @param int $productId
     * @param int|null $parentProductId
     * @return $this
     */
    public function addProductFilter($productId, $parentProductId = null)
    {
        if ($parentProductId) {
            $query = '(main_table.product_id = ' . $productId . ' AND main_table.parent_id IS NOT NULL)';
            $this->getSelect()->where($query);
        } else {
            $query = '(main_table.parent_product_id = ' . $productId . ' OR (main_table.product_id = '
                . $productId . ' AND main_table.parent_id = 0))';
            $this->getSelect()->where($query);
        }

        $this->conditionsForGroupBy[] = [
            'field' => $query,
            'condition' => []
        ];
        return $this;
    }

    /**
     * Add product sku filter to collection
     *
     * @param [] $condition
     * @return $this
     */
    public function addSkuFilter($condition)
    {
        $this->conditionsForGroupBy[] = [
            'field' => 'main_table.sku',
            'condition' => $condition
        ];
        $resultCondition = $this->_getConditionSql('sku', $condition);
        $this->getSelect()->where($resultCondition, null, Select::TYPE_CONDITION);
        return $this;
    }

    /**
     * Add product name filter to collection
     *
     * @param [] $condition
     * @return $this
     */
    public function addProductNameFilter($condition)
    {
        $this->conditionsForGroupBy[] = [
            'field' => 'main_table.product_name',
            'condition' => $condition
        ];
        $resultCondition = $this->_getConditionSql('product_name', $condition);
        $this->getSelect()->where($resultCondition, null, Select::TYPE_CONDITION);
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
            $this->addFieldToFilter('store_id', ['in' => $storeIds]);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addFilterForChart()
    {
        $this->getSelect()
            ->reset(\Magento\Framework\DB\Select::GROUP)
            ->reset(\Magento\Framework\DB\Select::WHERE);

        $this->addGroupByFilter();
        return $this;
    }
}
