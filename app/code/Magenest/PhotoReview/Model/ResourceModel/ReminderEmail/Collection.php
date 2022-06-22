<?php
namespace Magenest\PhotoReview\Model\ResourceModel\ReminderEmail;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';
    public function _construct()
    {
        $this->_init('Magenest\PhotoReview\Model\ReminderEmail','Magenest\PhotoReview\Model\ResourceModel\ReminderEmail');
    }
}