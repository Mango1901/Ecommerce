<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Block\Adminhtml\View;

use Magento\Backend\Block\Template\Context;
use Aheadworks\AdvancedReports\Model\Source\Period as PeriodSource;
use Aheadworks\AdvancedReports\Model\Filter\Period as PeriodFilter;
use Aheadworks\AdvancedReports\Model\Config;
use Aheadworks\AdvancedReports\Model\Period as PeriodModel;

/**
 * Class Period
 *
 * @package Aheadworks\AdvancedReports\Block\Adminhtml\View
 */
class Period extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_AdvancedReports::view/period.phtml';

    /**
     * @var PeriodSource
     */
    private $periodSource;

    /**
     * @var PeriodFilter
     */
    private $periodFilter;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var PeriodModel
     */
    private $periodModel;

    /**
     * Period constructor.
     * @param Context $context
     * @param PeriodSource $periodSource
     * @param PeriodFilter $periodFilter
     * @param Config $config
     * @param PeriodModel $periodModel
     * @param array $data
     */
    public function __construct(
        Context $context,
        PeriodSource $periodSource,
        PeriodFilter $periodFilter,
        Config $config,
        PeriodModel $periodModel,
        $data = []
    ) {
        parent::__construct($context, $data);
        $this->periodSource = $periodSource;
        $this->periodFilter = $periodFilter;
        $this->config = $config;
        $this->periodModel = $periodModel;
    }

    /**
     * Retrieve date range types
     *
     * @return []
     */
    public function getOptions()
    {
        return array_merge(
            $this->periodSource->getOptions(),
            [['value' => PeriodFilter::PERIOD_TYPE_CUSTOM, 'label' => __('Custom date range')]]
        );
    }

    /**
     * Retrieve date range periods
     *
     * @return []
     */
    public function getRanges()
    {
        $result = [];
        $rangeList = $this->periodSource->getRangeList($this->getLocaleTimezone());
        foreach ($rangeList as $key => $value) {
            /** @var \DateTime $from */
            $from = $value['from'];
            /** @var \DateTime $to */
            $to = $value['to'];
            $result[$key] = [
                'from' => $from->format('M d, Y'),
                'to' => $to->format('M d, Y'),
            ];
        }
        return $result;
    }

    /**
     * Retieve current period
     *
     * @return []
     */
    public function getPeriod()
    {
        return $this->periodFilter->getPeriod();
    }

    /**
     * Retrieve first calendar date
     *
     * @return string
     */
    public function getEarliestCalendarDateAsString()
    {
        $date = new \DateTime(
            $this->periodModel->getFirstAvailableDateAsString(),
            $this->periodFilter->getLocaleTimezone()
        );
        return $date->format('Y-m-d');
    }

    /**
     * Retrieve latest calendar date
     *
     * @return string
     */
    public function getLatestCalendarDateAsString()
    {
        $date = new \DateTime('now', $this->periodFilter->getLocaleTimezone());
        return $date->format('Y-m-d');
    }

    /**
     * Retrieve locale timezone
     *
     * @return \DateTimeZone
     */
    public function getLocaleTimezone()
    {
        return $this->periodFilter->getLocaleTimezone();
    }

    /**
     * Retrieve first day of week
     *
     * @return int
     */
    public function getFirstDayOfWeek()
    {
        return $this->config->getLocaleFirstday();
    }
}
