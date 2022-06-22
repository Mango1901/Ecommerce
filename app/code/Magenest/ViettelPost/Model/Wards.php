<?php
namespace Magenest\ViettelPost\Model;

use Magento\Framework\Model\AbstractModel;

class Wards extends AbstractModel
{
    const TABLE_NAME = 'magenest_viettelpost_wards';
    const PRIMARY_KEY = 'wards_id';

    protected $_eventPrefix = 'wards';

    protected $_idFieldName = self::PRIMARY_KEY;

    protected function _construct()
    {
        $this->_init(\Magenest\ViettelPost\Model\ResourceModel\Wards::class);
    }
}
