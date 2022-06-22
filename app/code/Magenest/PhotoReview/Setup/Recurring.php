<?php
namespace Magenest\PhotoReview\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class Recurring implements InstallSchemaInterface
{
    /** @var \Magento\Review\Model\ResourceModel\Review\CollectionFactory  */
    protected $_reviewCollection;

    /** @var \Magenest\PhotoReview\Model\ResourceModel\ReviewDetail */
    protected $_photoReviewResource;

    /** @var \Magento\Review\Model\RatingFactory */
    protected $_ratingFactory;

    /** @var \Psr\Log\LoggerInterface  */
    protected $_logger;

    /**
     * Recurring constructor.
     *
     * @param \Magento\Review\Model\ResourceModel\Review\CollectionFactory $collectionFactory
     * @param \Magenest\PhotoReview\Model\ResourceModel\ReviewDetail $reviewDetail
     * @param \Magento\Review\Model\RatingFactory $ratingFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $collectionFactory,
        \Magenest\PhotoReview\Model\ResourceModel\ReviewDetail $reviewDetail,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        \Psr\Log\LoggerInterface $logger
    ){
        $this->_reviewCollection = $collectionFactory;
        $this->_photoReviewResource = $reviewDetail;
        $this->_ratingFactory = $ratingFactory;
        $this->_logger = $logger;
    }

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if(version_compare('1.0.0', $context->getVersion(), '=')){
            $this->updateRatingSummary();
        }
        $installer->endSetup();
    }

    protected function updateRatingSummary()
    {
        try{
            $reviewCollection = $this->_reviewCollection->create()->addFieldToFilter(
                'status_id',
                \Magento\Review\Model\Review::STATUS_APPROVED
            );
            /** @var \Magento\Review\Model\Review $review */
            foreach ($reviewCollection->getItems() as $review){
                if($reviewId = $review->getReviewId()){
                    $ratingSummary = $this->getRatingSummary($reviewId);
                    $summary = $ratingSummary->getSum();
                    $count = $ratingSummary->getCount();
                    if($count > 0){
                        $ratingSum = ceil($summary/$count);
                        $updateData = [
                            'review_id' => $reviewId,
                            'rating_sum' => $ratingSum
                        ];
                        $this->_photoReviewResource->updateRecord($updateData);
                    }
                }
            }
        }catch (\Exception $exception){
            $this->_logger->critical($exception->getMessage());
        }
    }

    /**
     * @param $reviewId
     *
     * @return array|null
     */
    protected function getRatingSummary($reviewId)
    {
        return $this->_ratingFactory->create()->getReviewSummary($reviewId);
    }
}