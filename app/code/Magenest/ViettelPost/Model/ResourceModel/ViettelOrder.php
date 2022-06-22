<?php
namespace Magenest\ViettelPost\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magenest\ViettelPost\Model\ViettelOrder as ViettelOrderModel;

class ViettelOrder extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ViettelOrderModel::TABLE_NAME, ViettelOrderModel::PRIMARY_KEY);
        $this->_isPkAutoIncrement = false;
        $this->_useIsObjectNew = true;
    }
}
