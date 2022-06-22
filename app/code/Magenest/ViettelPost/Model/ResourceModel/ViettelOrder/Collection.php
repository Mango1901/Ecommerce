<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/10/16
 * Time: 10:21
 */

namespace Magenest\ViettelPost\Model\ResourceModel\ViettelOrder;

use Magenest\ViettelPost\Model\ViettelOrder;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = ViettelOrder::PRIMARY_KEY;

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ViettelOrder::class, \Magenest\ViettelPost\Model\ResourceModel\ViettelOrder::class);
    }
}
