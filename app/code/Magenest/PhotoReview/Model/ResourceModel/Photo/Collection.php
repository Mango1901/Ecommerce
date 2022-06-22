<?php
namespace Magenest\PhotoReview\Model\ResourceModel\Photo;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'photo_id';

    public function _construct()
    {
        $this->_init('Magenest\PhotoReview\Model\Photo','Magenest\PhotoReview\Model\ResourceModel\Photo');
    }
}