<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/10/16
 * Time: 10:21
 */

namespace Magenest\MapList\Model\ResourceModel\Location;

use Magenest\MapList\Helper\Constant;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = Constant::LOCATION_TABLE_ID;

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Constant::LOCATION_MODEL, Constant::LOCATION_RESOURCE_MODEL);
    }
}
