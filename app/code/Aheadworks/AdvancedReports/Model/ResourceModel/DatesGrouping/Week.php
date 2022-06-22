<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping;

use Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping\AbstractResource;

/**
 * Class Week
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping
 */
class Week extends AbstractResource
{
    /**
     * @var string
     */
    const KEY = 'week';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_arep_weeks', 'start_date');
    }

    /**
     * {@inheritdoc}
     */
    public function updateTable()
    {
        $maxWeekDateStr = $this->getConnection()->fetchOne('SELECT MAX(end_date) FROM ' . $this->getMainTable());
        $fromDate = $this->getFromDate($maxWeekDateStr);
        $toDate = $this->getToDate();
        $intervals = [];

        // If main table is empty
        if (!$maxWeekDateStr) {
            $firstWeekDay = $this->config->getLocaleFirstday();
            $curWeekDay = $fromDate->format('N');
            if ($curWeekDay > $firstWeekDay) {
                $firstWeekDay += 6;
            }
            $diff = abs($curWeekDay - $firstWeekDay);
            $startDate = $fromDate->format('Y-m-d');
            $fromDate->modify('+' . $diff . ' day');
            $endDate = $fromDate->format('Y-m-d');
            $fromDate->modify('+1 day');
            $intervals[] = ['start_date' => $startDate, 'end_date' => $endDate];
        }
        while ($fromDate < $toDate) {
            // If main table is empty
            if (!$maxWeekDateStr) {
                $startDate = $fromDate->format('Y-m-d');
                $fromDate->modify('+1 week')->modify('-1 day');
                $endDate = $fromDate->format('Y-m-d');
                $fromDate->modify('+1 day');
            } else {
                $fromDate->modify('+1 day');
                $startDate = $fromDate->format('Y-m-d');
                $fromDate->modify('+1 week')->modify('-1 day');
                $endDate = $fromDate->format('Y-m-d');
            }

            $intervals[] = ['start_date' => $startDate, 'end_date' => $endDate];
        }
        $this->addPeriodToTable($this->getMainTable(), $intervals);
    }
}
