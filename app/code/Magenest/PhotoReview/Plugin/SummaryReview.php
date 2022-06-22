<?php
namespace Magenest\PhotoReview\Plugin;

use Magenest\PhotoReview\Model\ResourceModel\ReviewDetail\CollectionFactory;

class SummaryReview
{
    protected $_ratingSummary = null;

    /** @var \Magenest\PhotoReview\Model\ResourceModel\ReviewDetail\CollectionFactory  */
    protected $_photoReviewDetailCollection;

    /** @var \Magento\Review\Model\RatingFactory */
    protected $_ratingFactory;

    /** @var \Psr\Log\LoggerInterface  */
    protected $_logger;

    public function __construct(
        \Magenest\PhotoReview\Model\ResourceModel\ReviewDetail\CollectionFactory $photoReviewDetailCollection,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        \Psr\Log\LoggerInterface $logger
    ){
        $this->_photoReviewDetailCollection = $photoReviewDetailCollection;
        $this->_ratingFactory = $ratingFactory;
        $this->_logger = $logger;
    }

    public function afterAggregate(
        \Magento\Review\Model\Review $subject,
        $result
    ){
        if($result instanceof \Magento\Review\Model\Review && $result->getStatusId() == \Magento\Review\Model\Review::STATUS_APPROVED){
            try{
                $reviewId = $subject->getReviewId();
                $summary = $this->getRatingSummary($reviewId)->getSum();
                $count = $this->getRatingSummary($reviewId)->getCount();
                $ratingSum = ceil($summary/$count);
                $reviewDetail = $this->_photoReviewDetailCollection->create()->addFieldToFilter('review_id', $reviewId)->getFirstItem();
                $reviewDetail->setRatingSum($ratingSum)->save();
            }catch (\Exception $exception){
                $this->_logger->critical($exception->getMessage());
            }
        }
        return $result;
    }

    /**
     * @param $reviewId
     *
     * @return array|null
     */
    public function getRatingSummary($reviewId)
    {
        if ($this->_ratingSummary == null) {
            $this->_ratingSummary = $this->_ratingFactory->create()->getReviewSummary($reviewId);
        }
        return $this->_ratingSummary;
    }
}