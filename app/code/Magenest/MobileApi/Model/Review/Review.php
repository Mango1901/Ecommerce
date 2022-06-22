<?php
namespace Magenest\MobileApi\Model\Review;

use Magenest\MobileApi\Api\Data\ReviewInterface;
use Magento\Framework\Api\AbstractSimpleObject;

class Review extends AbstractSimpleObject implements ReviewInterface
{
    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Get customer id
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->_get(self::CUSTOMER_ID);
    }

    /**
     * Get review detail
     *
     * @return string
     */
    public function getDetail()
    {
        return $this->_get(self::DETAIL);
    }

    /**
     * Get customer nickname
     *
     * @return string
     */
    public function getNickname()
    {
        return $this->_get(self::NICKNAME);
    }

    /**
     * Get review entity
     *
     * @return string
     */
    public function getReviewEntity()
    {
        return $this->_get(self::REVIEW_ENTITY);
    }

    /**
     * Get review type
     *
     * @return int
     */
    public function getReviewType()
    {
        return $this->_get(self::REVIEW_TYPE);
    }

    /**
     * Get review status
     *
     * @return int
     */
    public function getReviewStatus()
    {
        return $this->_get(self::REVIEW_STATUS);
    }

    /**
     * Get Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_get(self::TITLE);
    }

    /**
     * Set ID
     *
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Set Detail
     *
     * @param string $detail
     *
     * @return ReviewInterface
     */
    public function setDetail($detail)
    {
        return $this->setData(self::DETAIL, $detail);
    }

    /**
     * Set customer ID
     *
     * @param int|null $customerId
     *
     * @return ReviewInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @inheritdoc
     *
     * @param string $nickName
     *
     * @return ReviewInterface
     */
    public function setNickname($nickName)
    {
        return $this->setData(self::NICKNAME, $nickName);
    }

    /**
     * Set review entity
     *
     * @param string $entity
     *
     * @return $this
     */
    public function setReviewEntity($entity)
    {
        return $this->setData(self::REVIEW_ENTITY, $entity);
    }

    /**
     * Set review type
     *
     * @param int $type
     *
     * @return $this
     */
    public function setReviewType(int $type)
    {
        return $this->setData(self::REVIEW_TYPE, $type);
    }

    /**
     * Set review status
     *
     * @param int $status
     *
     * @return $this
     */
    public function setReviewStatus($status)
    {
        return $this->setData(self::REVIEW_STATUS, $status);
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Get create time
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
    }

    /**
     * Set create time
     *
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get ENTITY_PK_VALUE
     *
     * @return int
     */
    public function getEntityPkValue()
    {
        return $this->_get(self::ENTITY_PK_VALUE);
    }

    /**
     * Set ENTITY_PK_VALUE
     *
     * @param int $id
     *
     * @return $this
     */
    public function setEntityPkValue($id)
    {
        return $this->setData(self::ENTITY_PK_VALUE, $id);
    }

    /**
     * Get store
     *
     * @return array
     */
    public function getStores()
    {
        return $this->_get(self::STORES);
    }

    /**
     * Set store
     *
     * @param array $stores
     *
     * @return $this
     */
    public function setStores(array $stores)
    {
        return $this->setData(self::STORES, $stores);
    }

    /**
     * Get store id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * Set store ID
     *
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Get review ratings.
     *
     * @return \Magenest\MobileApi\Api\Data\RatingVoteInterface[]
     */
    public function getRatings() {
        return $this->_get(self::RATINGS);
    }

    /**
     * Set review ratings.
     *
     * @param \Magenest\MobileApi\Api\Data\RatingVoteInterface[] $ratings
     *
     * @return Review|void
     */
    public function setRatings($ratings) {
        return $this->setData(self::RATINGS, $ratings);
    }
}
