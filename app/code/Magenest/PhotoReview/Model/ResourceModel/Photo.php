<?php
namespace Magenest\PhotoReview\Model\ResourceModel;

class Photo extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function _construct()
    {
        $this->_init('magenest_photoreview_photo','photo_id');
    }
    public function insertMuiltiRecord($data){
        $connection = $this->getConnection();
        $connection->insertMultiple($this->getMainTable(), $data);
    }
}