<?php
namespace Magenest\MobileApi\Model;

use Magenest\MobileApi\Model\Review\Save;
use Magenest\MobileApi\Api\ReviewRepositoryInterface;
use Magenest\MobileApi\Api\Data\ReviewInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class ReviewRepository implements ReviewRepositoryInterface
{
    /** @var Save */
    private $commandSave;

    /**
     * ReviewRepository constructor.
     *
     * @param Save $commandSave
     */
    public function __construct(
        Save $commandSave
    ) {
        $this->commandSave = $commandSave;
    }

    /**
     * Save review
     *
     * @param ReviewInterface $review
     *
     * @return ReviewInterface|array
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(ReviewInterface $review)
    {
        return $this->commandSave->execute($review);
    }
}
