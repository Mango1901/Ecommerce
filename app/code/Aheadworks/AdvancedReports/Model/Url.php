<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Aheadworks\AdvancedReports\Model\Filter;
use Aheadworks\AdvancedReports\Model\Source\Groupby as GroupbySource;

/**
 * Class Url
 *
 * @package Aheadworks\AdvancedReports\Model
 */
class Url
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var Filter\Period
     */
    private $periodFilter;

    /**
     * @var Filter\Groupby
     */
    private $groupbyFilter;

    /**
     * @param RequestInterface $request
     * @param UrlInterface $urlBuilder
     * @param Filter\Period $periodFilter
     * @param Filter\Groupby $groupbyFilter
     */
    public function __construct(
        RequestInterface $request,
        UrlInterface $urlBuilder,
        Filter\Period $periodFilter,
        Filter\Groupby $groupbyFilter
    ) {
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
        $this->periodFilter = $periodFilter;
        $this->groupbyFilter = $groupbyFilter;
    }

    /**
     * Retrieve url for report
     *
     * @param string $report
     * @param string $reportTo
     * @param [] $params
     * @return string
     */
    public function getUrl($report, $reportTo, $params = [])
    {
        $params = array_merge(
            $params,
            [
                'period_from' => $this->periodFilter->getPeriodFrom()->format('Y-m-d'),
                'period_to'   => $this->periodFilter->getPeriodTo()->format('Y-m-d'),
                'period_type' => Filter\Period::PERIOD_TYPE_CUSTOM,
                'brc' => $this->getBrcParam($report, $reportTo)
            ]
        );

        return $this->urlBuilder->getUrl('advancedreports/' . $reportTo . '/index', ['_query' => $params]);
    }

    /**
     * Retrieve url for report by period
     *
     * @param [] $item
     * @param string $report
     * @param string $reportTo
     * @param [] $paramsFromRequest
     * @return []
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getUrlByPeriod($item, $report, $reportTo, $paramsFromRequest = [])
    {
        switch ($this->groupbyFilter->getCurrentGroupByKey()) {
            case GroupbySource::TYPE_DAY:
                $startDate = $endDate = new \DateTime($item['date'], $this->periodFilter->getLocaleTimezone());
                $params = [
                    'group_by' => GroupbySource::TYPE_DAY
                ];
                break;
            case GroupbySource::TYPE_WEEK:
                $startDate = new \DateTime($item['start_date'], $this->periodFilter->getLocaleTimezone());
                $endDate = new \DateTime($item['end_date'], $this->periodFilter->getLocaleTimezone());
                $params = [
                    'group_by' => GroupbySource::TYPE_DAY
                ];
                break;
            case GroupbySource::TYPE_MONTH:
                $startDate = new \DateTime($item['start_date'], $this->periodFilter->getLocaleTimezone());
                $endDate = new \DateTime($item['end_date'], $this->periodFilter->getLocaleTimezone());
                $params = [
                    'group_by' => GroupbySource::TYPE_WEEK
                ];
                break;
            case GroupbySource::TYPE_QUARTER:
                $startDate = new \DateTime($item['start_date'], $this->periodFilter->getLocaleTimezone());
                $endDate = new \DateTime($item['end_date'], $this->periodFilter->getLocaleTimezone());
                $params = [
                    'group_by' => GroupbySource::TYPE_MONTH
                ];
                break;
            case GroupbySource::TYPE_YEAR:
                $startDate = new \DateTime($item['start_date'], $this->periodFilter->getLocaleTimezone());
                $endDate = new \DateTime($item['end_date'], $this->periodFilter->getLocaleTimezone());
                $params = [
                    'group_by' => GroupbySource::TYPE_QUARTER
                ];
                break;
        }

        $periodFrom = $this->periodFilter->getPeriodFrom();
        $periodTo = $this->periodFilter->getPeriodTo();
        if ($startDate < $periodFrom) {
            $startDate = $periodFrom;
        }
        if ($endDate > $periodTo) {
            $endDate = $periodTo;
        }

        $params = array_merge(
            $params,
            $paramsFromRequest,
            [
                'period_from' => $startDate->format('Y-m-d'),
                'period_to'   => $endDate->format('Y-m-d'),
                'period_type' => Filter\Period::PERIOD_TYPE_CUSTOM,
                'brc' => $this->getBrcParam($report, $reportTo)
            ]
        );

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'url' => $this->urlBuilder->getUrl('advancedreports/' . $reportTo . '/index', ['_query' => $params])
        ];
    }

    /**
     * Retrieve brc param (for breadcrumbs)
     *
     * @param string $report
     * @param string $reportTo
     * @return string
     */
    private function getBrcParam($report, $reportTo)
    {
        $brc = $this->request->getParam('brc') ?: $report;
        return $brc . '-' . $reportTo;
    }
}
