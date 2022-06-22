<?php
namespace Magenest\RewardPoints\Model\ResourceModel\Account\Grid;

/**
 * Class Collection
 * @package Magenest\RewardPoints\Model\ResourceModel\Account\Grid
 */
class Collection  extends  \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    /**
     *
     */
    protected function _renderFiltersBefore()
    {
        $joinTable = $this->getTable('customer_entity');
        $this->getSelect()->join(
            $joinTable.' as ce', 'main_table.customer_id = ce.entity_id',
            ['email','firstname','lastname']
        );
        parent::_renderFiltersBefore();
    }
}
