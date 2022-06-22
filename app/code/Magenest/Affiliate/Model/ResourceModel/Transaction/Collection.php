<?php


namespace Magenest\Affiliate\Model\ResourceModel\Transaction;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\DB\Select;

/**
 * Class Collection
 * @package Magenest\Affiliate\Model\ResourceModel\Transaction
 */
class Collection extends AbstractCollection
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'transaction_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'affiliate_transaction_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'transaction_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\Affiliate\Model\Transaction', 'Magenest\Affiliate\Model\ResourceModel\Transaction');
    }

    /**
     * @param string $field
     *
     * @return string
     */
    public function getFieldTotal($field = 'amount')
    {
        $this->_renderFilters();

        $sumSelect = clone $this->getSelect();
        $sumSelect->reset(Select::ORDER);
        $sumSelect->reset(Select::LIMIT_COUNT);
        $sumSelect->reset(Select::LIMIT_OFFSET);
        $sumSelect->reset(Select::COLUMNS);

        $sumSelect->columns("SUM(`$field`)");

        return $this->getConnection()->fetchOne($sumSelect, $this->_bindParams);
    }

    /**
     * @param $day
     *
     * @return $this
     */
    public function addHoldingDaysToFilter($day)
    {
        $interval = "INTERVAL $day DAY";
        $this->getSelect()->where('`created_at` + ' . $interval . ' <= NOW()');

        return $this;
    }
}
