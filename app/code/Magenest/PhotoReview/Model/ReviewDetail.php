<?php
namespace Magenest\PhotoReview\Model;

class ReviewDetail extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'magenest_reviewdetail';

    public function _construct()
    {
        $this->_init('Magenest\PhotoReview\Model\ResourceModel\ReviewDetail');
    }
    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}