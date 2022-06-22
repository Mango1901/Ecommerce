<?php
namespace Magenest\MobileApi\Api;

use Magenest\MobileApi\Api\Data\ReviewInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

interface ReviewRepositoryInterface
{
    /**
     * Save review.
     *
     * @param ReviewInterface $review
     *
     * @return ReviewInterface|array
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function save(ReviewInterface $review);
}
