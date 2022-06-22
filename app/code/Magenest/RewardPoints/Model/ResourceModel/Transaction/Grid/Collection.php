<?php

namespace Magenest\RewardPoints\Model\ResourceModel\Transaction\Grid;

/**
 * Class Collection
 * @package Magenest\RewardPoints\Model\ResourceModel\Transaction\Grid
 */
class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    /**
     *
     */
    protected function _renderFiltersBefore()
    {
        $joinTable1 = $this->getTable('customer_entity');
        $joinTable2 = $this->getTable('magenest_rewardpoints_rule');
        $joinTable3 = $this->getTable('magenest_rewardpoints_expired');
        $this->getSelect()->join($joinTable1 . ' as ce', 'main_table.customer_id = ce.entity_id', ['email']);
        $this->getSelect()->joinLeft($joinTable2 . ' as mrr', 'main_table.rule_id = mrr.id', ['title'])->group('main_table.id');
        $this->getSelect()->joinLeft($joinTable3 . ' as expired_table', 'main_table.id = expired_table.transaction_id', ['expired_date'])->group('main_table.id');

        parent::_renderFiltersBefore();
    }

    /**
     * @param string $model
     * @param string $resourceModel
     * @return \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
     */
    protected function _init($model, $resourceModel)
    {
        $this->addFilterToMap('id', 'main_table.id');
        $this->addFilterToMap('customer_id', 'main_table.customer_id');
        $this->addFilterToMap('order_id', 'order_id.order_id');
        $this->addFilterToMap('product_id', 'main_table.product_id');
        $this->addFilterToMap('rule_id', 'main_table.rule_id');
        $this->addFilterToMap('points_change', 'main_table.points_change');
        $this->addFilterToMap('points_after', 'main_table.points_after');
        $this->addFilterToMap('insertion_date', 'main_table.insertion_date');
        $this->addFilterToMap('comment', 'main_table.comment');
        $this->addFilterToMap('expired', 'expired_table.expired_date');
        return parent::_init($model, $resourceModel);
    }

    /**
     * Retrieve collection items
     *
     * @return array|\Magento\Framework\Api\Search\DocumentInterface[]|\Magento\Framework\DataObject[]
     */
    public function getItems()
    {
        $this->load();
        $items = $this->_items;
        $itemsNew = [];
        if (!empty($items)) {
            foreach ($items as $item) {
                if (isset($item['rule_id'])) {
                    switch ($item['rule_id']) {
                        case 0:
                            $item['title'] = __("Redeem points");
                            break;
                        case -1:
                            $item['title'] = __("Points from admin");
                            if ($item['comment'] === '' || $item['comment'] === null) $item['comment'] = __('Points from admin');
                            break;
                        case -2:
                            $item['title'] = __("Referral code points");
                            break;
                        case -4:
                            $item['title'] = __("Deduct received points");
                            break;
                        case -5:
                            $item['title'] = __("Return applied points");
                            break;
                    }
                }
                $itemsNew[] = $item;
            }

            $this->_items = $itemsNew;
        }

        return $this->_items;
    }

}
