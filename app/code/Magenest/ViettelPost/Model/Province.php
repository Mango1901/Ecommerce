<?php
namespace Magenest\ViettelPost\Model;

use Magento\Framework\Model\AbstractModel;

class Province extends AbstractModel
{
    const TABLE_NAME = 'magenest_viettelpost_province';
    const PRIMARY_KEY = 'province_id';

    protected $_eventPrefix = 'province';

    protected $_idFieldName = self::PRIMARY_KEY;

    protected function _construct()
    {
        $this->_init(\Magenest\ViettelPost\Model\ResourceModel\Province::class);
    }
}
