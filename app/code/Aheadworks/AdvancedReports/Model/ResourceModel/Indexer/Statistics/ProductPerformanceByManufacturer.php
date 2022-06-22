<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics;

/**
 * Class ProductPerformanceByManufacturer
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics
 */
class ProductPerformanceByManufacturer extends ProductPerformance
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_arep_product_performance_manufacturer', 'id');
    }

    /**
     * {@inheritdoc}
     */
    protected function process()
    {
        $columns = $this->getColumns('children');
        $columns['manufacturer'] = 'manufacturer_value.value';

        /* @var $manufacturerAttr \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
        $manufacturerAttr = $this->attributeRepository->get('catalog_product', 'manufacturer');
        $manufacturerTable = $manufacturerAttr->getBackendTable();

        $select =
            $this->joinChildrenItems()
            ->columns($columns)
            ->joinLeft(
                ['item_manufacturer' => $manufacturerTable],
                'item_manufacturer. ' . $this->getCatalogLinkField() . ' = main_table.product_id AND 
                item_manufacturer.attribute_id = ' . $manufacturerAttr->getId(),
                []
            )->joinLeft(
                ['manufacturer_value' => $this->getTable('eav_attribute_option_value')],
                'item_manufacturer.value = manufacturer_value.option_id AND manufacturer_value.store_id = 0',
                []
            )
            ->where('manufacturer_value.value IS NOT NULL')
            ->group($this->getGroupByFields(['manufacturer_value.value']));
        $select = $this->addFilterByCreatedAt($select, 'order');

        $this->getConnection()->query($select->insertFromSelect($this->getIdxTable(), array_keys($columns)));
    }
}
