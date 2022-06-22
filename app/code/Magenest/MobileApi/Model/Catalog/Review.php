<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Model\Catalog;

use Magento\Framework\Api\AbstractSimpleObject;
use Magenest\MobileApi\Api\Data\Catalog\ReviewInterface;

/**
 * Class Review
 * @package Magenest\MobileApi\Model\Catalog
 */
class Review extends AbstractSimpleObject implements ReviewInterface
{
    /** @const */
    const KEY_REVIEWS = 'reviews';
    const KEY_REVIEW_SUMMARY = 'review_summary';

    /**
     * {@inheritdoc}
     */
    public function getReviews()
    {
        return $this->_get(self::KEY_REVIEWS);
    }

    /**
     * {@inheritdoc}
     */
    public function getReviewSummary()
    {
        return $this->_get(self::KEY_REVIEW_SUMMARY);
    }

    /**
     * {@inheritdoc}
     */
    public function setReviews($reviews)
    {
        return $this->setData(self::KEY_REVIEWS, $reviews);
    }

    /**
     * {@inheritdoc}
     */
    public function setReviewSummary($reviewSummary)
    {
        return $this->setData(self::KEY_REVIEW_SUMMARY, $reviewSummary);
    }
}
