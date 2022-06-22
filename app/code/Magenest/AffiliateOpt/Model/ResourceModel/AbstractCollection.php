<?php

namespace Magenest\AffiliateOpt\Model\ResourceModel;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Magenest\AffiliateOpt\Helper\Data;
use Magenest\AffiliateOpt\Model\Source\Period;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class AbstractCollection
 * @package Magenest\AffiliateOpt\Model\ResourceModel
 */
abstract class AbstractCollection extends SearchResult
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Collection constructor.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param RequestInterface $request
     * @param Data $helperData
     * @param string $mainTable
     * @param string $resourceModel
     *
     * @throws LocalizedException
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        RequestInterface $request,
        Data $helperData,
        $mainTable,
        $resourceModel
    ) {
        $this->_request = $request;
        $this->helperData = $helperData;

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * @return $this
     */
    public function addStoreToFilter()
    {
        $mpFilter = $this->getFilters();
        if (isset($mpFilter['store']) && $mpFilter['store']) {
            $this->getSelect()->where('main_table.store_id = ?', $mpFilter['store']);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function addDateToFilter()
    {
        $dateRange = $this->getDateRange();

        if (isset($dateRange[0]) && $dateRange[0] !== null) {
            $this->getSelect()->where('main_table.created_at >= ?', $dateRange[0] . " 00:00:00");
        }
        if (isset($dateRange[1]) && $dateRange[1] !== null) {
            $this->getSelect()->where('main_table.created_at <= ?', $dateRange[1] . " 23:59:59");
        }

        return $this;
    }

    /**
     * @param null $from
     * @param null $to
     *
     * @return array
     */
    protected function getDateRange($from = null, $to = null)
    {
        if ($from === null) {
            $from = isset($this->_request->getParam('mpFilter')['startDate'])
                ? $this->_request->getParam('mpFilter')['startDate']
                : (($this->_request->getParam('startDate') !== null) ? $this->_request->getParam('startDate') : null);
        }
        if ($to === null) {
            $to = isset($this->_request->getParam('mpFilter')['endDate'])
                ? $this->_request->getParam('mpFilter')['endDate']
                : (($this->_request->getParam('endDate') !== null) ? $this->_request->getParam('endDate') : null);
        }

        return [$from, $to];
    }

    /**
     * @return mixed
     */
    public function getFilters()
    {
        return $this->_request->getParam('mpFilter', []);
    }

    /**
     * @return string
     */
    public function getPeriod()
    {
        $filters = $this->getFilters();
        $defaultFilters = $this->_request->getParam('filters', []);
        if (!isset($filters['period']) && isset($defaultFilters['period'])) {
            $filters['period'] = $defaultFilters['period'];
        }

        $period = '%Y-%m-%d';
        if (isset($filters['period'])) {
            switch ($filters['period']) {
                case Period::WEEK:
                    $period = '%Y-%u';
                    break;
                case Period::MONTH:
                    $period = '%Y-%m';
                    break;
                case Period::YEAR:
                    $period = '%Y';
                    break;
                default:
            }
        }

        return $period;
    }
}
