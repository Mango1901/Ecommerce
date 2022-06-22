<?php
namespace Magenest\PhotoReview\Controller\Review;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    /** @var \Magenest\PhotoReview\Model\Session  */
    protected $_sessionPhotoReview;

    /** @var \Magento\Review\Model\ReviewFactory  */
    protected $_reviewFactory;

    /** @var \Magento\Review\Model\ResourceModel\Review  */
    protected $_reviewResource;

    /** @var \Psr\Log\LoggerInterface  */
    protected $_logger;

    public function __construct(
        \Magenest\PhotoReview\Model\Session $sessionPhotoReview,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Review\Model\ResourceModel\Review $reviewResource,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Action\Context $context
    ){
        $this->_sessionPhotoReview = $sessionPhotoReview;
        $this->_reviewFactory = $reviewFactory;
        $this->_reviewResource = $reviewResource;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        try{
            $reviewId = isset($params['reviewId']) ? $params['reviewId'] : null;
            $photoId = isset($params['photoId']) ? $params['photoId'] : null;
            if($reviewId == null || $photoId == null){
                throw new \Exception(__());
            }
            $this->_sessionPhotoReview->setData('photoId', $photoId);
            $this->_sessionPhotoReview->setData('reviewId', $reviewId);
        }catch (\Exception $exception){
            $this->_logger->critical($exception->getMessage());
        }
        return $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
    }
}