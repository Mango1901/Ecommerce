<?php

namespace Magenest\PhotoReview\Model\ResourceModel;

class ReviewDetail extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function _construct()
    {
        $this->_init('magenest_photoreview_detail', 'custom_id');
    }

    public function updateRecord($updateData)
    {
        try {
            $connection = $this->getConnection();
            $sql        = $connection->select()->from(
                $this->getMainTable(),
                'custom_id'
            )->where(
                'review_id = ?',
                $updateData['review_id']
            );
            $customId   = $connection->fetchOne($sql);
            if ($customId) {
                $where = $connection->quoteInto($this->getIdFieldName() . ' = ?', $customId);
                $connection->update($this->getMainTable(), $updateData, $where);
            } else {
                $data = [
                    'review_id'  => $updateData['review_id'],
                    'rating_sum' => $updateData['rating_sum']
                ];
                $connection->insert($this->getMainTable(), $data);
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
}