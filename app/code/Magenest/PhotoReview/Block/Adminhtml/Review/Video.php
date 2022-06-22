<?php

namespace Magenest\PhotoReview\Block\Adminhtml\Review;

use Magento\Framework\Registry;

class Video extends \Magento\Backend\Block\Template
{
    protected $_template = 'Magenest_PhotoReview::review/video.phtml';

    /**
     * @var \Magenest\PhotoReview\Model\PhotoFactory
     */
    protected $_photoFactory;

    /** @var Registry|null  */
    protected $_coreRegistry = null;

    /**
     * @var \Magenest\PhotoReview\Model\Video\Uploader
     */
    protected $_videoUpload;

    /**
     * Photo constructor.
     * @param \Magenest\PhotoReview\Model\Video\Uploader $videoUpload
     * @param \Magenest\PhotoReview\Model\PhotoFactory $photoFactory
     * @param Registry $coreRegistry
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magenest\PhotoReview\Model\Video\Uploader $videoUpload,
        \Magenest\PhotoReview\Model\PhotoFactory $photoFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->_videoUpload = $videoUpload;
        $this->_photoFactory = $photoFactory;
        $this->_coreRegistry = $coreRegistry;
        if ($this->_coreRegistry->registry('review_data')) {
            $this->setReviewId($this->_coreRegistry->registry('review_data')->getReviewId());
        }
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getAllPhoto(){
        $review_data = $this->_coreRegistry->registry('review_data');
        $reviewId = $review_data->getReviewId();
        $collection = $this->_photoFactory->create()
            ->getCollection()
            ->addFieldToFilter("review_id",$reviewId);
        return $collection->getItems();
    }

    /**
     * @param $path
     * @return string
     */
    public function getVideoUrl($path){
        $url = $this->_videoUpload->getBaseUrl().$path;
        return $url;
    }

}
