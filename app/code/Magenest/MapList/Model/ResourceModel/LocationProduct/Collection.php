<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 10/10/16
 * Time: 21:00
 */

namespace Magenest\MapList\Model\ResourceModel\LocationProduct;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magenest\MapList\Helper\Constant;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(Constant::LOCATION_PRODUCT_MODEL, Constant::LOCATION_PRODUCT_RESOURCE_MODEL);
    }
}
