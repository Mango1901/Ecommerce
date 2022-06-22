<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\Source;

use Magento\Framework\Locale\ListsInterface;
use Aheadworks\AdvancedReports\Model\Config;

/**
 * Class Period
 *
 * @package Aheadworks\AdvancedReports\Model\Source
 */
class Period
{
    /**#@+
     * Constants defined for the source model
     */
    const TYPE_TODAY = 'today';
    const TYPE_YESTERDAY = 'yesterday';
    const TYPE_LAST_7_DAYS = 'last_7_days';
    const TYPE_LAST_WEEK = 'last_week';
    const TYPE_LAST_BUSINESS_WEEK = 'last_business_week';
    const TYPE_THIS_MONTH = 'this_month';
    const TYPE_LAST_MONTH = 'last_month';
    /**#@-*/

    /**
     * @var []
     */
    private $weekdays;

    /**
     * @var []
     */
    private $rangeCache;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ListsInterface
     */
    private $localeLists;

    /**
     * @param Config $config
     * @param ListsInterface $localeList
     */
    public function __construct(
        Config $config,
        ListsInterface $localeList
    ) {
        $this->config = $config;
        $this->localeLists = $localeList;
    }

    /**
     * Get options
     *
     * @return []
     */
    public function getOptions()
    {
        return [
            ['value' => self::TYPE_TODAY, 'label' => __('Today')],
            ['value' => self::TYPE_YESTERDAY, 'label' => __('Yesterday')],
            ['value' => self::TYPE_LAST_7_DAYS, 'label' => __('Last 7 days')],
            ['value' => self::TYPE_LAST_WEEK, 'label' => $this->getLastWeekLabel()],
            ['value' => self::TYPE_LAST_BUSINESS_WEEK, 'label' => $this->getLastBusinessWeekLabel()],
            ['value' => self::TYPE_THIS_MONTH, 'label' => __('This month')],
            ['value' => self::TYPE_LAST_MONTH, 'label' => __('Last month')],
        ];
    }

    /**
     * Get range list
     *
     * @param \DateTimeZone $timezone
     * @return []
     */
    public function getRangeList(\DateTimeZone $timezone)
    {
        if (null != $this->rangeCache) {
            return $this->rangeCache;
        }
        $result = [];
        $result[self::TYPE_TODAY] = [
            'from' => new \DateTime('now', $timezone), 'to' => new \DateTime('now', $timezone)
        ];
        $result[self::TYPE_YESTERDAY] = [
            'from' => new \DateTime('yesterday', $timezone), 'to' => new \DateTime('yesterday', $timezone)
        ];
        $result[self::TYPE_LAST_7_DAYS] = [
            'from' => new \DateTime('6 days ago', $timezone), 'to' => new \DateTime('now', $timezone)
        ];

        $firstWeekDay = $this->getWeekdayKeyByNum($this->getFirstWeekDay());
        $lastWeekDay = $this->getWeekdayKeyByNum($this->getLastWeekDay());
        $from = new \DateTime("previous $firstWeekDay", $timezone);
        $to = new \DateTime("previous $lastWeekDay", $timezone);
        if ($to->getTimestamp() < $from->getTimestamp()) {
            $from->modify('-7 days');
        }
        $result[self::TYPE_LAST_WEEK] = [
            'from' => $from, 'to' => $to
        ];

        $bWeek = $this->getBusinessWeekDays();
        $firstBusinessWeekDay = $this->getWeekdayKeyByNum($bWeek[0]);
        $lastBusinessWeekDay = $this->getWeekdayKeyByNum(end($bWeek));
        $from = new \DateTime("previous $firstBusinessWeekDay", $timezone);
        $to = new \DateTime("previous $lastBusinessWeekDay", $timezone);
        if ($to->getTimestamp() < $from->getTimestamp()) {
            $from->modify('-7 days');
        }
        $result[self::TYPE_LAST_BUSINESS_WEEK] = [
            'from' => $from, 'to' => $to
        ];

        $result[self::TYPE_THIS_MONTH] = [
            'from' => new \DateTime('first day of this month', $timezone), 'to' => new \DateTime('now', $timezone)
        ];
        $result[self::TYPE_LAST_MONTH] = [
            'from' => new \DateTime('first day of last month', $timezone),
            'to' => new \DateTime('last day of last month', $timezone)
        ];
        $this->rangeCache = $result;
        return $this->rangeCache;
    }

    /**
     * Get last week label
     *
     * @return string
     */
    private function getLastWeekLabel()
    {
        $firstDayNum = $this->getFirstWeekDay();
        $lastDayNum = $this->getLastWeekDay();
        return __('Last week')
            . ' (' . substr($this->getWeekdayByDayNum($firstDayNum), 0, 3) . ' - '
            . substr($this->getWeekdayByDayNum($lastDayNum), 0, 3) . ')';
    }

    /**
     * Get last business week label
     *
     * @return string
     */
    private function getLastBusinessWeekLabel()
    {
        $bWeek = $this->getBusinessWeekDays();

        $fWD = ucfirst($this->getWeekdayKeyByNum($bWeek[0]));
        $lWD = ucfirst($this->getWeekdayKeyByNum(end($bWeek)));
        return __('Last business week') . ' (' . $fWD . ' - ' . $lWD . ')';
    }

    /**
     * Get business week days
     *
     * @return []
     */
    private function getBusinessWeekDays()
    {
        $week = [0, 1, 2, 3, 4, 5, 6];
        $week = array_diff($week, explode(',', $this->config->getLocaleWeekend()));
        return array_values($week);
    }

    /**
     * Get weekday by day num
     *
     * @param int $dayNum
     * @return string
     */
    private function getWeekdayByDayNum($dayNum)
    {
        if (null == $this->weekdays) {
            $this->weekdays = $this->localeLists->getOptionWeekdays();
        }
        foreach ($this->weekdays as $day) {
            if ($day['value'] == $dayNum) {
                return $day['label'];
            }
        }
        return '';
    }

    /**
     * Get weekday key by num
     *
     * @param int $index
     * @return string|null
     */
    private function getWeekdayKeyByNum($index)
    {
        $days = [
            0 => 'sun',
            1 => 'mon',
            2 => 'tue',
            3 => 'wed',
            4 => 'thu',
            5 => 'fri',
            6 => 'sat',
        ];
        return isset($days[$index]) ? $days[$index] : null;
    }

    /**
     * Get first week day
     *
     * @return int
     */
    private function getFirstWeekDay()
    {
        $firstDay = $this->config->getLocaleFirstday();
        return $firstDay ? $firstDay : 0;
    }

    /**
     * Get last week day
     *
     * @return int
     */
    private function getLastWeekDay()
    {
        $firstDayNum = $this->getFirstWeekDay();
        $lastDayNum = $firstDayNum + 6;
        return $lastDayNum > 6 ? $lastDayNum - 7 : $lastDayNum;
    }
}
