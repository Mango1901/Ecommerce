<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\ResourceModel;

/**
 * Class Manufacturer
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel
 */
class Manufacturer extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_arep_manufacturer', 'id');
    }
}
