<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/10/16
 * Time: 10:21
 */

namespace Magenest\ViettelPost\Model\ResourceModel\Province;

use Magenest\ViettelPost\Model\Province;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = Province::PRIMARY_KEY;

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Province::class, \Magenest\ViettelPost\Model\ResourceModel\Province::class);
    }
}
