<?php
namespace Magenest\RewardPoints\Model;

class MyReferral extends \Magento\Framework\Model\AbstractModel{
    public function _construct()
    {
        $this->_init('Magenest\RewardPoints\Model\ResourceModel\MyReferral');
    }
}