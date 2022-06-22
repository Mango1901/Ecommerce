<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/10/16
 * Time: 10:21
 */

namespace Magenest\ViettelPost\Model\ResourceModel\Wards;

use Magenest\ViettelPost\Model\Wards;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = Wards::PRIMARY_KEY;

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Wards::class, \Magenest\ViettelPost\Model\ResourceModel\Wards::class);
    }
}
