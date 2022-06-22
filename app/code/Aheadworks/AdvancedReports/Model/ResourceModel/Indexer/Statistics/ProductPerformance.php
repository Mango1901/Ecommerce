<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics;

/**
 * Class ProductPerformance
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics
 */
class ProductPerformance extends AbstractResource
{
    /**
     * @var string
     */
    protected $period;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_arep_product_performance', 'id');
    }

    /**
     * {@inheritdoc}
     */
    protected function process()
    {
        $columns = $this->getColumns();
        $columns['payment_method'] = 'order_payment.method';

        $select =
            $this->joinParentItems()
            ->join(
                ['order_payment' => $this->getTable('sales_order_payment')],
                'order_payment.parent_id = order.entity_id',
                []
            )
            ->columns($columns)
            ->group($this->getGroupByFields(['order_payment.method']));
        $select = $this->addFilterByCreatedAt($select, 'order');

        $this->getConnection()->query($select->insertFromSelect($this->getIdxTable(), array_keys($columns)));
    }

    /**
     * Retrieve base columns for table
     *
     * @param string $type
     * @return []
     */
    protected function getColumns($type = 'parent')
    {
        $this->period = $this->getPeriod('order.created_at');
        $columns = [
            'period' => $this->period,
            'store_id' => 'order.store_id',
            'order_status' => 'order.status',
            'product_id' => 'main_table.product_id',
            'product_name' => 'main_table.name',
            'sku' => 'IFNULL(catalog.sku, CONCAT(main_table.sku, " (product was deleted)"))',
            'order_items_count' => 'SUM(COALESCE(main_table.qty_ordered, 0))',
            'to_global_rate' => 'order.base_to_global_rate'
        ];

        if ($type == 'children') {
            $columns = array_merge($columns, $this->getColumnsForChildrenItems());
        } else {
            $columns = array_merge($columns, $this->getColumnsForParentItems());
        }

        return $columns;
    }

    /**
     * Retrieve base group by for table
     *
     * @param [] $additionalFields
     * @return []
     */
    protected function getGroupByFields($additionalFields)
    {
        return array_merge(
            [
                'order.status',
                $this->period,
                'order.store_id',
                'main_table.product_id',
                'order.base_to_global_rate'
            ],
            $additionalFields
        );
    }

    /**
     * Retrieve columns for parent items query
     *
     * @return []
     */
    private function getColumnsForParentItems()
    {
        $columns = [
            'subtotal' => 'SUM(COALESCE(main_table.base_row_total, 0.0))',
            'tax' => 'SUM(COALESCE(main_table.base_tax_amount, 0.0))',
            'discount' => 'ABS(SUM(COALESCE(children.discount_amount, main_table.base_discount_amount, 0.0)))',
            'total' => '(SUM(COALESCE(main_table.base_row_total, 0.0))
                + SUM(COALESCE(main_table.base_tax_amount, 0.0))
                - SUM(COALESCE(children.discount_amount, main_table.base_discount_amount, 0.0)))',
            'invoiced' => '(SUM(COALESCE(children.row_invoiced, main_table.base_row_invoiced, 0.0))
                + SUM(COALESCE(children.tax_invoiced, main_table.base_tax_invoiced, 0.0))
                - SUM(COALESCE(children.discount_invoiced, main_table.base_discount_invoiced, 0.0)))',
            'refunded' => '(SUM(COALESCE(children.amount_refunded, main_table.base_amount_refunded, 0.0))
                + SUM(COALESCE(children.tax_refunded, main_table.base_tax_refunded, 0.0))
                - SUM(COALESCE(children.discount_refunded, main_table.base_discount_refunded, 0.0)))'
        ];

        return $columns;
    }

    /**
     * Retrieve columns for child items query
     *
     * @return []
     */
    private function getColumnsForChildrenItems()
    {
        $columns = [
            'parent_id' => 'parent.product_id',
            'subtotal' => 'SUM(COALESCE(configurable.base_row_total, main_table.base_row_total, 0.0))',
            'tax' => 'SUM(COALESCE(configurable.base_tax_amount, main_table.base_tax_amount, 0.0))',
            'discount' => 'ABS(SUM(COALESCE(configurable.base_discount_amount, main_table.base_discount_amount, 0.0)))',
            'total' => '(SUM(COALESCE(configurable.base_row_total, main_table.base_row_total, 0.0))
                       + SUM(COALESCE(configurable.base_tax_amount, main_table.base_tax_amount, 0.0))
                       - SUM(COALESCE(configurable.base_discount_amount, main_table.base_discount_amount, 0.0)))',
            'invoiced' => '(SUM(COALESCE(configurable.base_row_invoiced, main_table.base_row_invoiced, 0.0))
                       + SUM(COALESCE(configurable.base_tax_invoiced, main_table.base_tax_invoiced, 0.0))
                       - SUM(COALESCE(configurable.base_discount_invoiced, main_table.base_discount_invoiced, 0.0)))',
            'refunded' => '(SUM(COALESCE(configurable.base_amount_refunded, main_table.base_amount_refunded, 0.0))
                       + SUM(COALESCE(configurable.base_tax_refunded, main_table.base_tax_refunded, 0.0))
                       - SUM(COALESCE(configurable.base_discount_refunded, main_table.base_discount_refunded, 0.0)))'
        ];

        return $columns;
    }

    /**
     * Retrieve base query
     *
     * @return \Magento\Framework\DB\Select
     */
    private function baseQuery()
    {
        $this->disableStagingPreview();

        $select = $this->getConnection()->select()
            ->from(['main_table' => $this->getTable('sales_order_item')], [])
            ->joinLeft(
                ['order' => $this->getTable('sales_order')],
                'order.entity_id = main_table.order_id',
                []
            )->joinLeft(
                ['catalog' => $this->getTable('catalog_product_entity')],
                'main_table.product_id = catalog.entity_id',
                []
            );
        return $select;
    }

    /**
     * Retrieve query by join parent items
     *
     * @return \Magento\Framework\DB\Select
     */
    protected function joinParentItems()
    {
        $orderItemTable = $this->getTable('sales_order_item');
        $select =
            $this->baseQuery()
                ->joinLeft(
                    ['children' => new \Zend_Db_Expr(
                        '(SELECT 
                        IF(t_item.base_discount_amount = 0, SUM(t_item2.base_discount_amount), 
                        t_item.base_discount_amount) AS `discount_amount`,
                        IF(t_item.base_discount_invoiced = 0, SUM(t_item2.base_discount_invoiced), 
                        t_item.base_discount_invoiced) AS `discount_invoiced`,
                        IF(t_item.base_discount_refunded = 0, SUM(t_item2.base_discount_refunded), 
                        t_item.base_discount_refunded) AS `discount_refunded`,
                        IF(t_item.base_row_invoiced = 0, SUM(t_item2.base_row_invoiced), 
                        t_item.base_row_invoiced) AS `row_invoiced`,
                        IF(t_item.base_amount_refunded = 0, SUM(t_item2.base_amount_refunded), 
                        t_item.base_amount_refunded) AS `amount_refunded`,
                        IF(t_item.base_tax_invoiced = 0, SUM(t_item2.base_tax_invoiced), 
                        t_item.base_tax_invoiced) AS `tax_invoiced`,
                        IF(t_item.base_tax_refunded = 0, SUM(t_item2.base_tax_refunded), 
                        t_item.base_tax_refunded) AS `tax_refunded`,
                        t_item.item_id AS `parent_id`
                        FROM ' . $this->getTable('sales_order') . ' AS `t_order`
                        INNER JOIN ' . $orderItemTable . ' AS `t_item` ON 
                        (t_item.order_id = t_order.entity_id AND t_item.parent_item_id IS NULL)
                        INNER JOIN ' . $orderItemTable . ' AS `t_item2` ON (t_item2.order_id = t_order.entity_id AND '
                        . 't_item2.parent_item_id IS NOT NULL AND t_item2.parent_item_id = t_item.item_id) '
                        . 'GROUP BY t_item.item_id)'
                    )],
                    'main_table.item_id = children.parent_id',
                    []
                )->where('main_table.parent_item_id IS NULL');
        return $select;
    }

    /**
     * Retrieve query by join children items
     *
     * @return \Magento\Framework\DB\Select
     */
    protected function joinChildrenItems()
    {
        $this->disableStagingPreview();
        $orderItemTable = $this->getTable('sales_order_item');

        $select =
            $this->baseQuery()
                ->joinLeft(
                    ['configurable' => $orderItemTable],
                    '(order.entity_id = configurable.order_id AND main_table.parent_item_id = configurable.item_id 
                    AND configurable.product_type IN ("configurable"))',
                    []
                )->joinLeft(
                    ['parent' => $orderItemTable],
                    '(order.entity_id = parent.order_id AND main_table.parent_item_id = parent.item_id)',
                    []
                )->joinLeft(
                    ['bundle_item_price' => $this->getBundleItemsPrice()],
                    '(order.entity_id = bundle_item_price.order_id AND '
                    . 'main_table.item_id = bundle_item_price.parent_item_id)',
                    []
                )->where('(main_table.product_type <> "bundle" OR bundle_item_price.price = 0)')
                ->where('(main_table.product_type <> "configurable")');
        return $select;
    }
}
