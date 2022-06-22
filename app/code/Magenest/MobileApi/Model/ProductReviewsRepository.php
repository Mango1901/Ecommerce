<?php
namespace Magenest\MobileApi\Model;

use Magento\Review\Model\ResourceModel\Rating\Option\Vote\CollectionFactory as RatingCollectionFactory;
use Magento\Review\Model\ResourceModel\Review\Product\Collection as ReviewCollection;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory as ReviewCollectionFactory;

class ProductReviewsRepository implements \Magenest\MobileApi\Api\ProductReviewsInterface
{

    /** @var RatingCollectionFactory  */
    protected $ratingCollectionFactory;
    /**
     * @var ReviewCollectionFactory
     */
    private $reviewCollectionFactory;

    /**
     * @param RatingCollectionFactory $ratingCollectionFactory
     * @param ReviewCollectionFactory $collectionFactory
     */
    public function __construct(
        RatingCollectionFactory $ratingCollectionFactory,
        ReviewCollectionFactory $collectionFactory
    ) {
        $this->ratingCollectionFactory = $ratingCollectionFactory;
        $this->reviewCollectionFactory = $collectionFactory;
    }

    /**
     * Get product reviews by productId
     *
     * @param int $storeId
     * @param int $productId
     * @return array|\Magenest\MobileApi\Api\Data\ReviewInterface[]
     */
    public function getProductReviewsById(int $storeId, int $productId)
    {
        $reviewsCollection = $this->reviewCollectionFactory->create()->addStoreFilter(
            $storeId
        )->addStatusFilter(
            \Magento\Review\Model\Review::STATUS_APPROVED
        )->addEntityFilter(
            'product',
            $productId
        )->setDateOrder()
        ->addRateVotes();

        $reviews = $reviewsCollection->getItems();
        $results = [];
        $i = 0;
        foreach ($reviews as $review) {
            $results[$i] = $review->getData();
            $results[$i][\Magenest\MobileApi\Api\Data\ReviewInterface::RATINGS] = $review->getRatingVotes()->getData();
            unset($results[$i]['rating_votes']);
            $i++;
        }

        return $results;
    }
}
