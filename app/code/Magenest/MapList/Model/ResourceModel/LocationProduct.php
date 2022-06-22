<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 10/10/16
 * Time: 20:59
 */

namespace Magenest\MapList\Model\ResourceModel;

use Magenest\MapList\Helper\Constant;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class LocationProduct extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(Constant::LOCATION_PRODUCT_TABLE, Constant::LOCATION_PRODUCT_TABLE_ID);
    }
}
