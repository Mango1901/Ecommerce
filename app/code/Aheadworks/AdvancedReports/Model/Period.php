<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model;

use Aheadworks\AdvancedReports\Model\Source\Groupby;
use Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping\Factory as DatesGroupingFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\App\CacheInterface;
use Aheadworks\AdvancedReports\Model\Filter;
use Aheadworks\AdvancedReports\Model\Url as UrlModel;
use Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping;

/**
 * Class Period
 *
 * @package Aheadworks\AdvancedReports\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Period
{
    /**
     * @var string
     */
    const MIN_DATE_CACHE_KEY = 'aw_arep_period_firstdate';

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var DatesGroupingFactory
     */
    private $datesGroupingFactory;

    /**
     * @var Filter\Groupby
     */
    private $groupbyFilter;

    /**
     * @var UrlModel
     */
    private $urlModel;

    /**
     * @param TimezoneInterface $localeDate
     * @param CacheInterface $cache
     * @param DatesGroupingFactory $datesGroupingFactory
     * @param Filter\Groupby $groupbyFilter
     * @param UrlModel $urlModel
     */
    public function __construct(
        TimezoneInterface $localeDate,
        CacheInterface $cache,
        DatesGroupingFactory $datesGroupingFactory,
        Filter\Groupby $groupbyFilter,
        UrlModel $urlModel
    ) {
        $this->localeDate = $localeDate;
        $this->cache = $cache;
        $this->datesGroupingFactory = $datesGroupingFactory;
        $this->groupbyFilter = $groupbyFilter;
        $this->urlModel = $urlModel;
    }

    /**
     * Retrieve first available date as string
     *
     * @return string
     */
    public function getFirstAvailableDateAsString()
    {
        if (!$minDate = $this->cache->load(self::MIN_DATE_CACHE_KEY)) {
            $minDate = $this->datesGroupingFactory->create(DatesGrouping\Day::KEY)->getMinDate();
            $this->cache->save($minDate, self::MIN_DATE_CACHE_KEY, [], null);
        }
        return $minDate;
    }

    /**
     * Retrieve period label and url
     *
     * @param [] $item
     * @param [] $paramsFromRequest
     * @return []
     */
    public function getPeriod($item, $paramsFromRequest = [])
    {
        $period = $this->urlModel->getUrlByPeriod($item, 'salesoverview', 'productperformance', $paramsFromRequest);
        $periodLabel = $this->getPeriodAsString(
            $period['start_date'],
            $period['end_date'],
            $this->groupbyFilter->getCurrentGroupByKey()
        );
        return ['period_url' => $period['url'], 'period_label' => $periodLabel];
    }

    /**
     * Retrieve period as string
     *
     * @param \DateTime $from
     * @param \DateTime $to
     * @param string $groupType
     * @param boolean $isShowYear = true
     * @return string
     */
    private function getPeriodAsString($from, $to, $groupType, $isShowYear = true)
    {
        $value = '';
        switch($groupType) {
            case Groupby::TYPE_DAY:
                $value = $this->formatDate($from, $isShowYear);
                break;
            case Groupby::TYPE_WEEK:
                $value = $this->formatDate($from, $isShowYear) . ' - ' . $this->formatDate($to, $isShowYear);
                break;
            case Groupby::TYPE_MONTH:
                $value = $from->format($isShowYear ? 'M Y' : 'M');
                break;
            case Groupby::TYPE_QUARTER:
                $month = (integer)$from->format('m');
                $value = 'Q' . ceil($month / 3) . ' ' . $from->format('Y');
                break;
            case Groupby::TYPE_YEAR:
                $value = $from->format('Y');
                break;
        }
        return $value;
    }

    /**
     * Retrieve formatted date
     *
     * @param \DateTime $date
     * @param boolean $isShowYear
     * @return string
     */
    private function formatDate($date, $isShowYear)
    {
        $pattern = $isShowYear ? 'M d, Y' : 'M d';
        return $date->format($pattern);
    }
}
