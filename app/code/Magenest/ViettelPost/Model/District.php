<?php
namespace Magenest\ViettelPost\Model;

use Magento\Framework\Model\AbstractModel;

class District extends AbstractModel
{
    const TABLE_NAME = 'magenest_viettelpost_district';
    const PRIMARY_KEY = 'district_id';

    protected $_eventPrefix = 'district';

    protected $_idFieldName = self::PRIMARY_KEY;

    protected function _construct()
    {
        $this->_init(\Magenest\ViettelPost\Model\ResourceModel\District::class);
    }
}
