<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics;

/**
 * Class ProductPerformanceByCategory
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics
 */
class ProductPerformanceByCategory extends ProductPerformance
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_arep_product_performance_category', 'id');
    }

    /**
     * {@inheritdoc}
     */
    protected function process()
    {
        $columns = $this->getColumns();
        $columns['category_id'] = 'category.category_id';

        $select =
            $this->joinParentItems()
            ->join(
                ['category' => $this->getTable('catalog_category_product')],
                'main_table.product_id = category.product_id',
                []
            )
            ->columns($columns)
            ->group($this->getGroupByFields(['category.category_id']));
        $select = $this->addFilterByCreatedAt($select, 'order');

        $this->getConnection()->query($select->insertFromSelect($this->getIdxTable(), array_keys($columns)));
    }
}
