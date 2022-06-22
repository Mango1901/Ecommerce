<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model;

use Aheadworks\AdvancedReports\Model\DatesGroupingManagement;

/**
 * Class Cron
 *
 * @package Aheadworks\AdvancedReports\Model
 */
class Cron
{
    /**
     * @var DatesGroupingManagement
     */
    private $datesGroupingManagement;

    /**
     * @param DatesGroupingManagement $datesGroupingManagement
     */
    public function __construct(
        DatesGroupingManagement $datesGroupingManagement
    ) {
        $this->datesGroupingManagement = $datesGroupingManagement;
    }

    /**
     * Update dates grouping tables
     *
     * @return $this
     */
    public function execute()
    {
        $this->datesGroupingManagement->updateTables();
        return $this;
    }
}
