<?php
namespace Magenest\PhotoReview\Observer\Review;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Deleted implements ObserverInterface
{
    /** @var \Magenest\PhotoReview\Model\ResourceModel\Photo\CollectionFactory  */
    protected $photoReviewCollectionFactory;

    /** @var \Magenest\PhotoReview\Model\Photo\Uploader  */
    protected $_photoUpload;

    /** @var \Psr\Log\LoggerInterface  */
    protected $_logger;

    public function __construct(
        \Magenest\PhotoReview\Model\ResourceModel\Photo\CollectionFactory $photoReviewCollectionFactory,
        \Magenest\PhotoReview\Model\Photo\Uploader $photoUpload,
        \Psr\Log\LoggerInterface $logger
    ){
        $this->photoReviewCollectionFactory = $photoReviewCollectionFactory;
        $this->_photoUpload = $photoUpload;
        $this->_logger = $logger;
    }

    public function execute(Observer $observer)
    {
        try{
            $reviewModel = $observer->getEvent()->getObject();
            if($reviewModel instanceof \Magento\Review\Model\Review ){
                $id = $reviewModel->getReviewId();
                $collections = $this->photoReviewCollectionFactory->create()->addFieldToFilter('review_id',$id)->getItems();
                if(count($collections) > 0){
                    $count = 0;
                    $baseDir = $this->_photoUpload->getBaseDir();
                    /** @var \Magenest\PhotoReview\Model\Photo $photo */
                    foreach ($collections as $photo){
                        if($photo->getPhotoId()){
                            $path = $baseDir . $photo->getPath();
                            if(file_exists($path)){
                                unlink($path);
                            }
                            $count++;
                        }
                    }
                }
            }
        }catch (\Exception $exception){
            $this->_logger->critical($exception->getMessage());
            throw new \Exception($exception->getMessage());
        }
    }
}
 