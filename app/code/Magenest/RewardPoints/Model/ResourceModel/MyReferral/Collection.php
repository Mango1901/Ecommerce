<?php
namespace Magenest\RewardPoints\Model\ResourceModel\MyReferral;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected $_idFieldName = 'id';

    public function _construct()
    {
        $this->_init('Magenest\RewardPoints\Model\MyReferral','Magenest\RewardPoints\Model\ResourceModel\MyReferral');
    }
}