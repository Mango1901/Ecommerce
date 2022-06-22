<?php
namespace Magenest\ViettelPost\Model;

use Magento\Framework\Model\AbstractModel;

class ViettelWebhook extends AbstractModel
{
    const TABLE_NAME = 'magenest_viettelpost_webhook';
    const PRIMARY_KEY = 'id';

    protected $_eventPrefix = 'viettelpost_webhook';

    protected $_idFieldName = self::PRIMARY_KEY;

    protected function _construct()
    {
        $this->_init(\Magenest\ViettelPost\Model\ResourceModel\ViettelWebhook::class);
    }
}
