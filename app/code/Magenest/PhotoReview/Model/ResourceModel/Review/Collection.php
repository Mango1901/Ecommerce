<?php
namespace Magenest\PhotoReview\Model\ResourceModel\Review;

class Collection extends \Magento\Review\Model\ResourceModel\Review\Collection
{
    /**
     * Add entity filter
     *
     * @param int|string $entity
     * @param int|array $pkValue
     *
     * @return $this
     */
    public function addEntityFilter($entity, $pkValue)
    {
        $reviewEntityTable = $this->getReviewEntityTable();
        if (is_numeric($entity)) {
            $this->addFilter('entity', $this->getConnection()->quoteInto('main_table.entity_id=?', $entity), 'string');
        } elseif (is_string($entity)) {
            $this->_select->join(
                $reviewEntityTable,
                'main_table.entity_id=' . $reviewEntityTable . '.entity_id',
                ['entity_code']
            );

            $this->addFilter(
                'entity',
                $this->getConnection()->quoteInto($reviewEntityTable . '.entity_code=?', $entity),
                'string'
            );
        }

        $this->addFilter(
            'entity_pk_value',
            $this->getConnection()->quoteInto('main_table.entity_pk_value IN (?)', $pkValue),
            'string'
        );

        return $this;
    }
}
