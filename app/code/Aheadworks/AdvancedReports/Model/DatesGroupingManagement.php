<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model;

use Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping\Factory as DatesGroupingFactory;
use Magento\Framework\ObjectManagerInterface;
use Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping;

/**
 * Class DatesGroupingManagement
 *
 * @package Aheadworks\AdvancedReports\Model
 */
class DatesGroupingManagement
{
    /**
     * @var DatesGroupingFactory
     */
    private $datesGroupingFactory;

    /**
     * @param DatesGroupingFactory $datesGroupingFactory
     */
    public function __construct(
        DatesGroupingFactory $datesGroupingFactory
    ) {
        $this->datesGroupingFactory = $datesGroupingFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function updateTables()
    {
        $updateTableKeys = [
            DatesGrouping\Day::KEY,
            DatesGrouping\Week::KEY,
            DatesGrouping\Month::KEY,
            DatesGrouping\Quarter::KEY,
            DatesGrouping\Year::KEY
        ];
        foreach ($updateTableKeys as $updateTableKey) {
            $this->datesGroupingFactory->create($updateTableKey)->updateTable();
        }
    }
}
