<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Aheadworks\AdvancedReports\Model\DatesGroupingManagement;

/**
 * Class InstallData
 *
 * @package Aheadworks\AdvancedReports\Setup
 */
class InstallData implements InstallDataInterface
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
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->datesGroupingManagement->updateTables();
    }
}
