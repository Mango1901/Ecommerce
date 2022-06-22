<?php
namespace Magenest\Popup\Model\ResourceModel;

class Log extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
    public function _construct()
    {
        $this->_init('magenest_log','log_id');
    }
}