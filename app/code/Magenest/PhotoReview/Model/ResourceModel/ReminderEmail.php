<?php
namespace Magenest\PhotoReview\Model\ResourceModel;

class ReminderEmail extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function _construct()
    {
        $this->_init('magenest_photoreview_reminder_email','id');
    }

    public function updateMultiStatus($ids, $status, $field = 'id')
    {
        try{
            $size = count($ids);
            if(!is_array($ids)&&$size == 0) return;
            $this->getConnection()->update(
                $this->getMainTable(),
                ['status' => $status],
                ["$field IN (?)" => $ids]
            );
            return true;
        }catch (\Exception $exception){
           throw new \Exception($exception->getMessage());
        }
    }
}