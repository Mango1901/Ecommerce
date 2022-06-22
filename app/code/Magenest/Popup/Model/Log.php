<?php
namespace Magenest\Popup\Model;

class Log extends \Magento\Framework\Model\AbstractModel {
    public function _construct()
    {
        $this->_init('Magenest\Popup\Model\ResourceModel\Log');
    }
}