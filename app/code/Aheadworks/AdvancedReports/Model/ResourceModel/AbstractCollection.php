<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\ResourceModel;

use Aheadworks\AdvancedReports\Model\Config;
use Aheadworks\AdvancedReports\Model\Filter;

/**
 * Class AbstractCollection
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    const GROUP_TABLE_ALIAS = 'group_table';

    /**
     * @var string
     */
    protected $timeField = 'period';

    /**
     * @var bool
     */
    protected $topFilterForChart = false;

    /**
     * @var []
     */
    protected $conditionsForGroupBy = [];

    /**
     * @var AbstractCollection
     */
    private $collectionSelect;

    /**
     * @var Filter\Store
     */
    protected $storeFilter;

    /**
     * @var Filter\Groupby
     */
    protected $groupbyFilter;

    /**
     * @var Filter\Period
     */
    protected $periodFilter;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param Config $config
     * @param Filter\Store $storeFilter
     * @param Filter\Groupby $groupbyFilter
     * @param Filter\Period $periodFilter
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        Config $config,
        Filter\Store $storeFilter,
        Filter\Groupby $groupbyFilter,
        Filter\Period $periodFilter,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->config = $config;
        $this->groupbyFilter = $groupbyFilter;
        $this->periodFilter = $periodFilter;
        $this->storeFilter = $storeFilter;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Add group filter to collection
     *
     * @return $this
     */
    public function addGroupByFilter()
    {
        $periodFrom = $this->periodFilter->getPeriodFrom();
        $periodTo = $this->periodFilter->getPeriodTo();
        switch($this->groupbyFilter->getCurrentGroupByKey()) {
            case \Aheadworks\AdvancedReports\Model\Source\Groupby::TYPE_DAY:
                $this->addGroupByDay($periodFrom, $periodTo);
                break;
            case \Aheadworks\AdvancedReports\Model\Source\Groupby::TYPE_WEEK:
                $this->groupByTable($this->getTable('aw_arep_weeks'), $periodFrom, $periodTo);
                break;
            case \Aheadworks\AdvancedReports\Model\Source\Groupby::TYPE_MONTH:
                $this->groupByTable($this->getTable('aw_arep_month'), $periodFrom, $periodTo);
                break;
            case \Aheadworks\AdvancedReports\Model\Source\Groupby::TYPE_QUARTER:
                $this->groupByTable($this->getTable('aw_arep_quarter'), $periodFrom, $periodTo);
                break;
            case \Aheadworks\AdvancedReports\Model\Source\Groupby::TYPE_YEAR:
                $this->groupByTable($this->getTable('aw_arep_year'), $periodFrom, $periodTo);
                break;
        }
        return $this;
    }

    /**
     * Add period filter to collection
     *
     * @return $this
     */
    public function addPeriodFilter()
    {
        $from = $this->periodFilter->getPeriodFrom();
        $to = $this->periodFilter->getPeriodTo();
        $this->addFieldToFilter($this->timeField, [
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d')
        ]);
        return $this;
    }

    /**
     * Add order statuses filter to collection
     *
     * @return $this
     */
    public function addOrderStatusFilter()
    {
        $this->addFieldToFilter('order_status', ['in' => $this->getOrderStatuses()]);
        return $this;
    }

    /**
     * Add store filter to collection
     *
     * @return $this
     */
    public function addStoreFilter()
    {
        $storeIds = $this->storeFilter->getStoreIds();
        if (null != $storeIds) {
            $this->addFieldToFilter('store_id', ['in' => $storeIds]);
        }
        return $this;
    }

    /**
     * Add filter to collection for chart
     *
     * @return $this
     */
    public function addFilterForChart()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'storeFilter') {
            return $this->addStoreFilter();
        }
        if ($field == 'periodFilter') {
            $this->addPeriodFilter();
            $this->addOrderStatusFilter();
            return $this;
        }
        if ($field == 'store_id' || $field == 'order_status' || $field == $this->timeField) {
            return parent::addFieldToFilter($field, $condition);
        }
        // Apply filters for grid query
        return $this->addFilter($field, $condition, 'public');
    }

    /**
     * Retrieve totals
     *
     * @return []
     */
    public function getTotals()
    {
        $collectionSelect = clone $this->collectionSelect->getSelect();
        $collectionSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT)
            ->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);

        $totalSelect = clone $this->getSelect();
        $totalSelect->reset(\Magento\Framework\DB\Select::COLUMNS)
            ->reset(\Magento\Framework\DB\Select::FROM)
            ->reset(\Magento\Framework\DB\Select::GROUP)
            ->reset(\Magento\Framework\DB\Select::LIMIT_COUNT)
            ->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET)
            ->from(
                ['main_table' => new \Zend_Db_Expr(sprintf('(%s)', $collectionSelect))],
                $this->getTotalColumns()
            );
        return $this->getConnection()->fetchRow($totalSelect) ?: [];
    }

    /**
     * Retrieve chart rows
     *
     * @return []
     */
    public function getChartRows()
    {
        $collectionSelect = clone $this->collectionSelect;
        $collectionSelect
            ->addFilterForChart();
        $collectionSelect->getSelect()
            ->reset(\Zend_Db_Select::LIMIT_COUNT)
            ->reset(\Zend_Db_Select::LIMIT_OFFSET);

        $chartSelect = clone $this->getSelect();
        $chartSelect->reset(\Magento\Framework\DB\Select::COLUMNS)
            ->reset(\Magento\Framework\DB\Select::FROM)
            ->reset(\Magento\Framework\DB\Select::GROUP)
            ->reset(\Magento\Framework\DB\Select::LIMIT_COUNT)
            ->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET)
            ->reset(\Magento\Framework\DB\Select::ORDER)
            ->from(
                ['main_table' => new \Zend_Db_Expr(sprintf('(%s)', $collectionSelect->getSelect()))],
                ['*']
            );
        if ($this->topFilterForChart) {
            $chartSelect->order('order_items_count ' . self::SORT_ORDER_DESC)
                ->limit(10);
        }

        return $this->getConnection()->fetchAll($chartSelect) ?: [];
    }

    /**
     * Retrieve report columns
     *
     * @param boolean $addRate
     * @return []
     */
    abstract protected function getColumns($addRate = false);

    /**
     * Retrieve report total columns
     *
     * @param boolean $addRate
     * @return []
     */
    protected function getTotalColumns($addRate = false)
    {
        return $this->getColumns($addRate);
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        $this->collectionSelect = clone $this;
        // Change select for apply grid filters
        $this->getSelect()->reset()->from(
            ['main_table' => new \Zend_Db_Expr(sprintf('(%s)', $this->collectionSelect->getSelect()))],
            ['*']
        );
        parent::_renderFiltersBefore();
    }

    /**
     * Change main table
     *
     * @param string $suffix
     * @return $this
     */
    protected function changeMainTable($suffix)
    {
        $this->setMainTable($this->getMainTable() . $suffix);
        return $this;
    }

    /**
     * Add group by day to collection
     *
     * @param \DateTime $periodFrom
     * @param \DateTime $periodTo
     * @return $this
     */
    protected function addGroupByDay($periodFrom, $periodTo)
    {
        $table = $this->getTable('aw_arep_days');
        if ($periodFrom && $periodTo) {
            $this->getSelect()->where(
                '(' . self::GROUP_TABLE_ALIAS . '.date BETWEEN "' . $periodFrom->format('Y-m-d') . '" AND "'
                . $periodTo->format('Y-m-d') . '")'
            );
        }

        $this->getSelect()
            ->joinRight(
                [self::GROUP_TABLE_ALIAS => $table],
                $this->getConditionForGroupBy() . ' AND ' .
                $this->timeField . ' = ' . self::GROUP_TABLE_ALIAS . '.date AND ' .
                $this->_getConditionSql('main_table.order_status', ['in' => $this->getOrderStatuses()])
            );
        $this->getSelect()->group(self::GROUP_TABLE_ALIAS . '.date');
        return $this;
    }

    /**
     * Add group by table to collection
     *
     * @param string $table
     * @param \DateTime $periodFrom
     * @param \DateTime $periodTo
     * @return $this
     */
    protected function groupByTable($table, $periodFrom, $periodTo)
    {
        if ($periodFrom && $periodTo) {
            $this->getSelect()->where(self::GROUP_TABLE_ALIAS . '.start_date <= "' . $periodTo->format('Y-m-d') . '"');
            $this->getSelect()->where(self::GROUP_TABLE_ALIAS . '.end_date >= "' . $periodFrom->format('Y-m-d') . '"');
        }
        $this->getSelect()
            ->joinRight(
                [self::GROUP_TABLE_ALIAS => $table],
                $this->getConditionForGroupBy() . ' AND ' .
                $this->timeField . ' >= "' . $periodFrom->format('Y-m-d') . '" AND '
                . $this->timeField . ' <= "' . $periodTo->format('Y-m-d') . '"'
                . ' AND ' . $this->timeField . ' BETWEEN group_table.start_date AND group_table.end_date '
                . ' AND ' . $this->_getConditionSql('main_table.order_status', ['in' => $this->getOrderStatuses()])
            )
            ->group(self::GROUP_TABLE_ALIAS . '.start_date');
        return $this;
    }

    /**
     * Retrieve rate field if necessary
     *
     * @param boolean $addRate
     * @return $string
     */
    protected function getRateField($addRate = true)
    {
        return (null === $this->storeFilter->getStoreIds() && $addRate) ? ' * main_table.to_global_rate' : '';
    }

    /**
     * Get condition for group by day, week, month, quarter, year
     *
     * @return string
     */
    private function getConditionForGroupBy()
    {
        $joinCondition = '1=1';
        foreach ($this->conditionsForGroupBy as $condition) {
            $joinCondition .= ' AND ' . ($condition['condition']
                ? $this->_getConditionSql($condition['field'], $condition['condition'])
                : $condition['field']);
        }
        return $joinCondition;
    }

    /**
     * Retrieve order statuses from config
     *
     * @return []
     */
    private function getOrderStatuses()
    {
        return explode(',', $this->config->getOrderStatus());
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        if ($field == 'period') {
            switch($this->groupbyFilter->getCurrentGroupByKey()) {
                case \Aheadworks\AdvancedReports\Model\Source\Groupby::TYPE_DAY:
                    $field = 'date';
                    break;
                case \Aheadworks\AdvancedReports\Model\Source\Groupby::TYPE_WEEK:
                case \Aheadworks\AdvancedReports\Model\Source\Groupby::TYPE_MONTH:
                case \Aheadworks\AdvancedReports\Model\Source\Groupby::TYPE_QUARTER:
                case \Aheadworks\AdvancedReports\Model\Source\Groupby::TYPE_YEAR:
                    $field = 'start_date';
                    break;
            }
        }
        return parent::setOrder($field, $direction);
    }
}
