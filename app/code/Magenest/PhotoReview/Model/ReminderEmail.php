<?php
namespace Magenest\PhotoReview\Model;

class ReminderEmail extends \Magento\Framework\Model\AbstractModel
{
    const STATUS_QUEUE = 0;
    const STATUS_SENT = 1;
    const STATUS_FAIL = 2;
    const STATUS_CANCEL = 3;

    public function _construct()
    {
        $this->_init('Magenest\PhotoReview\Model\ResourceModel\ReminderEmail');
    }
}