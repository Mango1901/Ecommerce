<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/


namespace Aheadworks\AdvancedReports\Model\ResourceModel;

/**
 * Class CouponCode
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel
 */
class CouponCode extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_arep_coupon_code', 'id');
    }
}
