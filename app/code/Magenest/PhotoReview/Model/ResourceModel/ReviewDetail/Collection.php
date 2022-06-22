<?php
namespace Magenest\PhotoReview\Model\ResourceModel\ReviewDetail;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'custom_id';
    protected $_photoReviewTable = null;
    public function _construct()
    {
        $this->_init('Magenest\PhotoReview\Model\ReviewDetail','Magenest\PhotoReview\Model\ResourceModel\ReviewDetail');
    }
    protected function getPhotoReviewTable(){
        if($this->_photoReviewTable == null){
            $this->_photoReviewTable = $this->getTable('magenest_review_photo');
        }
        return $this->_photoReviewTable;
    }
}