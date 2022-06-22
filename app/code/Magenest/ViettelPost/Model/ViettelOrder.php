<?php
namespace Magenest\ViettelPost\Model;

use Magento\Framework\Model\AbstractModel;

class ViettelOrder extends AbstractModel
{
    const TABLE_NAME = 'magenest_viettelpost_order';
    const PRIMARY_KEY = 'shipment_id';

    protected $_eventPrefix = 'viettelpost_order';

    protected $_idFieldName = self::PRIMARY_KEY;

    protected function _construct()
    {
        $this->_init(\Magenest\ViettelPost\Model\ResourceModel\ViettelOrder::class);
    }
}
