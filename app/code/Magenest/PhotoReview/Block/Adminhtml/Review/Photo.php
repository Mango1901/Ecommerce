<?php
namespace Magenest\PhotoReview\Block\Adminhtml\Review;

use Magento\Framework\Registry;

class Photo extends \Magento\Backend\Block\Template
{
    protected $_template = 'Magenest_PhotoReview::review/photo.phtml';

    protected $_photoFactory;

    /** @var Registry|null  */
    protected $_coreRegistry = null;

    protected $_photoUpload;

    public function __construct(
        \Magenest\PhotoReview\Model\Photo\Uploader $photoUpload,
        \Magenest\PhotoReview\Model\PhotoFactory $photoFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->_photoUpload = $photoUpload;
        $this->_photoFactory = $photoFactory;
        $this->_coreRegistry = $coreRegistry;
        if ($this->_coreRegistry->registry('review_data')) {
            $this->setReviewId($this->_coreRegistry->registry('review_data')->getReviewId());
        }
        parent::__construct($context, $data);
    }

    public function getAllPhoto(){
        $review_data = $this->_coreRegistry->registry('review_data');
        $reviewId = $review_data->getReviewId();
        $collection = $this->_photoFactory->create()
            ->getCollection()
            ->addFieldToFilter("review_id",$reviewId);
        return $collection->getItems();
    }
    public function getPhotoUrl($path){
        $url = $this->_photoUpload->getBaseUrl().$path;
        return $url;
    }
}