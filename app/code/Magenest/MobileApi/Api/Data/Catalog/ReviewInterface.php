<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Api\Data\Catalog;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ReviewInterface
 * @package Magenest\MobileApi\Api\Data\Catalog
 */
interface ReviewInterface extends ExtensibleDataInterface
{
    /**
     * Get reviews
     *
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface[]
     * @since 102.0.0
     */
    public function getReviews();

    /**
     * Set review items
     *
     * @param \Magenest\MobileApi\Api\Data\DataObjectInterface[] $reviews
     * @return $this
     */
    public function setReviews($reviews);

    /**
     * Get review summary
     *
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface[]
     * @since 102.0.0
     */
    public function getReviewSummary();

    /**
     * Set top review
     *
     * @param \Magenest\MobileApi\Api\Data\DataObjectInterface[] $reviewSummary
     * @return $this
     */
    public function setReviewSummary($reviewSummary);
}
