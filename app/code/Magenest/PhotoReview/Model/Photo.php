<?php
namespace Magenest\PhotoReview\Model;

class Photo extends \Magento\Framework\Model\AbstractModel
{
    public function _construct()
    {
        $this->_init('Magenest\PhotoReview\Model\ResourceModel\Photo');
    }
}