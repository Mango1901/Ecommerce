<?php
/**
 * @copyright Copyright (c) magenest.com, Inc. (https://www.magenest.com)
 */

namespace Magenest\CustomAdvancedReports\Model\ResourceModel\Category;

class Collection extends \Aheadworks\AdvancedReports\Model\ResourceModel\Category\Collection
{
    protected function _initSelect()
    {
        $this->getSelect()
            ->from(['main_table' => $this->getMainTable()], [])
            ->columns($this->getColumns(true))
            ->join(
                ['gcc' => 'group_category_config'],
                'main_table.category_id = gcc.category',
                ['name as gcc_name']
            )->group('gcc_name');
        return $this;
    }

}
