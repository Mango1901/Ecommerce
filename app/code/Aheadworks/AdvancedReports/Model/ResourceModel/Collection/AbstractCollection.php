<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/


namespace Aheadworks\AdvancedReports\Model\ResourceModel\Collection;

use Magento\Catalog\Api\Data\CategoryInterface;

/**
 * @method AbstractCollection addGroupByDay()
 * @method AbstractCollection addGroupByWeek()
 * @method AbstractCollection addGroupByMonth()
 * @method AbstractCollection addGroupByQuarter()
 * @method AbstractCollection addGroupByYear()
 */
abstract class AbstractCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    const GROUP_TABLE_ALIAS = 'group_table';

    protected $_groupCallback = null;
    protected $_joinCallback = null;
    protected $_isGroupedByTimeUnit = false;

    protected $_timeField = 'main_table.created_at';

    protected $catalogLinkField;

    /**
     * Period from date
     *
     * @var \DateTime
     */
    protected $_from = null;

    /**
     * Period to date
     *
     * @var \DateTime
     */
    protected $_to = null;

    /**
     * Period from date (UTC)
     *
     * @var \DateTime
     */
    protected $_utcFrom = null;

    /**
     * Period to date (UTC)
     *
     * @var \DateTime
     */
    protected $_utcTo = null;

    /**
     * timezone offset
     *
     * @var int
     */
    protected $_timezoneOffset= null;

    /**
     * store ids for filter
     *
     * @var int[]|null
     */
    protected $_storeIds = null;

    /**
     * order statuses for filter
     *
     * @var int[]|null
     */
    protected $_statuses = null;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    protected $metadataPool;

    /**
     * Set up store ids to filter collection
     *
     * @param int[]|null $storeIds
     *
     * @return \Aheadworks\AdvancedReports\Model\ResourceModel\Collection\AbstractCollection
     */
    abstract public function addStoreFilter($storeIds);

    /**
     * Set up order statuses to filter collection
     *
     * @param array $statuses
     *
     * @return \Aheadworks\AdvancedReports\Model\ResourceModel\Collection\AbstractCollection
     */
    abstract public function addOrderStatusFilter($statuses);

    /**
     * AbstractCollection constructor.
     *
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory
     * @param \Psr\Log\LoggerInterface                                     $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager
     * @param \Magento\Framework\EntityManager\MetadataPool                $metadataPool
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool
    ) {
        $this->metadataPool = $metadataPool;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager);
    }

    /**
     * Set up date range to filter collection
     *
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return \Aheadworks\AdvancedReports\Model\ResourceModel\Collection\AbstractCollection
     */
    public function addPeriodFilter($from, $to)
    {
        $phpOffset = (new \DateTimeZone(date_default_timezone_get()))->getOffset(new \DateTime());
        $this->_timezoneOffset = $from->getOffset() - $phpOffset;
        $this->_utcFrom = clone $from;
        $this->_utcTo = clone $to;
        $this->_from = clone $from;
        $this->_to = clone $to;
        $this->_preparePeriodValues($this->_from, $this->_to);

        $this->addFieldToFilter($this->_timeField, [
            'from' => $this->_from->format('Y-m-d H:i:s'),
            'to' => $this->_to->format('Y-m-d H:i:s')
        ]);
        return $this;
    }

    /**
     * add group by day to collection
     *
     * @return \Aheadworks\AdvancedReports\Model\ResourceModel\Collection\AbstractCollection
     */
    protected function _addGroupByDay()
    {
        $table = $this->getTable('aw_arep_days');
        $alias = self::GROUP_TABLE_ALIAS;
        $joinCondition = "1=1";
        try {
            $joinCondition = implode(' ', $this->getSelect()->getPart(\Zend_Db_Select::WHERE));
            $this->getSelect()->reset(\Zend_Db_Select::WHERE);
        } catch (\Zend_Db_Select_Exception $e) {}

        if ($this->_utcFrom && $this->_utcTo) {
            $this->getSelect()->where(
                "({$alias}.date BETWEEN '{$this->_utcFrom->format('Y-m-d')}' AND '{$this->_utcTo->format('Y-m-d')}')"
            );
        }

        $this->getSelect()
            ->joinRight(
                [$alias => $table],
                "{$joinCondition}
                AND TIMESTAMPADD(SECOND, {$this->_timezoneOffset}, {$this->_timeField}) BETWEEN CONCAT({$alias}.date, ' 00:00:00')
                AND CONCAT({$alias}.date, ' 23:59:59')"
            );
        $this->getSelect()->group("{$alias}.date");
        $this->addFilterToMap('period', "{$alias}.date");
        return $this;
    }

    /**
     * add group by week to collection
     *
     * @return \Aheadworks\AdvancedReports\Model\ResourceModel\Collection\AbstractCollection
     */
    protected function _addGroupByWeek()
    {
        $this->_groupByTable($this->getTable('aw_arep_weeks'));
        return $this;
    }

    /**
     * add group by month to collection
     *
     * @return \Aheadworks\AdvancedReports\Model\ResourceModel\Collection\AbstractCollection
     */
    protected function _addGroupByMonth()
    {
        $this->_groupByTable($this->getTable('aw_arep_month'));
        return $this;
    }

    /**
     * add group by quarter to collection
     *
     * @return \Aheadworks\AdvancedReports\Model\ResourceModel\Collection\AbstractCollection
     */
    protected function _addGroupByQuarter()
    {
        $this->_groupByTable($this->getTable('aw_arep_quarter'));
        return $this;
    }

    /**
     * add group by year to collection
     *
     * @return \Aheadworks\AdvancedReports\Model\ResourceModel\Collection\AbstractCollection
     */
    protected function _addGroupByYear()
    {
        $this->_groupByTable($this->getTable('aw_arep_year'));
        return $this;
    }

    /**
     * @param string $table
     *
     * @return $this
     */
    protected function _groupByTable($table)
    {
        $alias = self::GROUP_TABLE_ALIAS;
        $joinCondition = "1=1";
        try {
            $joinCondition = implode(' ', $this->getSelect()->getPart(\Zend_Db_Select::WHERE));
            $this->getSelect()->reset(\Zend_Db_Select::WHERE);
        } catch (\Zend_Db_Select_Exception $e) {}

        if ($this->_utcFrom && $this->_utcTo) {
            $this->getSelect()->where("{$alias}.start_date <= '{$this->_utcTo->format('Y-m-d')}'");
            $this->getSelect()->where("{$alias}.end_date >= '{$this->_utcFrom->format('Y-m-d')}'");
        }

        $this->getSelect()
            ->joinRight(
                [$alias => $table],
                "({$joinCondition})
                AND TIMESTAMPADD(SECOND, {$this->_timezoneOffset}, {$this->_timeField}) BETWEEN CONCAT({$alias}.start_date, ' 00:00:00')
                AND CONCAT({$alias}.end_date, ' 23:59:59')"
            );
        $this->getSelect()->group(self::GROUP_TABLE_ALIAS . ".start_date");
        $this->addFilterToMap('period', self::GROUP_TABLE_ALIAS . ".start_date");
        return $this;
    }

    public function __call($name, $arguments)
    {
        if (strpos($name, 'joinCollectionModeItems') === 0) {
            $this->_joinCallback = ['_' . $name, $arguments];
            return $this;
        }
        if (strpos($name, 'addGroupBy') === 0) {
            $this->_groupCallback = ['_' . $name, $arguments];
            return $this;
        }
        return call_user_func(array($this, $name), $arguments);
    }

    protected function _renderFiltersBefore()
    {
        if (null !== $this->_joinCallback) {
            call_user_func(
                [$this, $this->_joinCallback[0]], $this->_joinCallback[1]
            );
        }
        //run group last
        if (null !== $this->_groupCallback && !$this->_isGroupedByTimeUnit) {
            $this->getSelect()->reset(\Zend_Db_Select::GROUP);
            call_user_func(
                [$this, $this->_groupCallback[0]], $this->_groupCallback[1]
            );
            $this->_isGroupedByTimeUnit = true;
        }
        parent::_renderFiltersBefore();
    }

    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $countSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $countSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $countSelect->reset(\Magento\Framework\DB\Select::COLUMNS);

        if (!count($this->getSelect()->getPart(\Magento\Framework\DB\Select::GROUP))) {
            $countSelect->columns(new \Zend_Db_Expr('COUNT(*)'));
            return $countSelect;
        }

        $countSelect->reset(\Magento\Framework\DB\Select::GROUP);
        $group = $this->getSelect()->getPart(\Magento\Framework\DB\Select::GROUP);
        foreach ($group as $key => $value) {
            $group[$key] = "IFNULL({$value}, 0)";
        }
        $countSelect->columns(new \Zend_Db_Expr(("COUNT(DISTINCT ".implode(", ", $group).")")));
        return $countSelect;
    }

    public function getTotalsItem()
    {
        $this->_renderFiltersBefore();
        $totalSelect = clone $this->getSelect();
        $totalSelect->reset(\Zend_Db_Select::GROUP);
        $totalSelect->reset(\Zend_Db_Select::LIMIT_COUNT);
        $totalSelect->reset(\Zend_Db_Select::LIMIT_OFFSET);
        $totals = new \Magento\Framework\DataObject($this->getConnection()->fetchRow($totalSelect));
        return $totals;
    }

    public function clear()
    {
        $this->_totalRecords = null;
        $this->_isFiltersRendered = false;
        return parent::clear();
    }

    /**
     * Prepare time and timezone for period filter
     *
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return \Aheadworks\AdvancedReports\Model\ResourceModel\Collection\AbstractCollection
     */
    protected function _preparePeriodValues(&$from, &$to)
    {
        $from->setTime(0,0,0);
        $to->setTime(23,59,59);
        $timezone = date_default_timezone_get();
        $from->setTimezone(new \DateTimeZone($timezone));
        $to->setTimezone(new \DateTimeZone($timezone));
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getCatalogLinkField()
    {
        if (null === $this->catalogLinkField) {
            $this->catalogLinkField = $this->metadataPool->getMetadata(CategoryInterface::class)->getLinkField();
        }
        return $this->catalogLinkField;
    }
}
