<?php
namespace Magenest\PhotoReview\Block\Product\View;

use Magenest\PhotoReview\Model\ResourceModel\ReviewDetail\Collection;
use Magenest\PhotoReview\Model\ResourceModel\ReviewDetail\CollectionFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

class CustomDetail extends \Magento\Framework\View\Element\Template
{
    protected $_template = "Magenest_PhotoReview::product/view/photo.phtml";

    /** @var \Magenest\PhotoReview\Model\ReviewDetailFactory $_cReviewDetailFactory */
    protected $_cReviewDetailFactory;

    /** @var \Magenest\PhotoReview\Model\ResourceModel\ReviewDetail $_cReviewDetailResource */
    protected $_cReviewDetailResource;

    protected $_cReviewDetailCollection;

    /** @var \Magenest\PhotoReview\Helper\Data $_helperData */
    protected $_helperData;

    protected $_photoReviewCollection;

    protected $_reviewModel;

    protected $_photoUpload;

    public function __construct(
        \Magenest\PhotoReview\Model\ReviewDetailFactory $reviewDetailFactory,
        \Magenest\PhotoReview\Model\ResourceModel\ReviewDetail $reviewResource,
        \Magenest\PhotoReview\Model\ResourceModel\Photo\CollectionFactory $collectionFactory,
        \Magenest\PhotoReview\Helper\Data $helperData,
        \Magenest\PhotoReview\Model\Photo\Uploader $photoUpload,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ){
        $this->_cReviewDetailFactory = $reviewDetailFactory;
        $this->_cReviewDetailResource = $reviewResource;
        $this->_photoReviewCollection = $collectionFactory;
        $this->_helperData = $helperData;
        $this->_photoUpload = $photoUpload;
        parent::__construct($context, $data);
    }
    public function setReview($review){
        $this->_reviewModel = $review;
    }
    public function getReview(){
        return $this->_reviewModel;
    }
    public function getCustomReviewDetail()
    {
        $data = null;
        $reviewModel = $this->getReview();
        if($reviewModel->getData('review_id')&&$reviewModel->getData('detail_id')){
            $data = $this->_helperData->getCustomReviewDetail($reviewModel->getData('review_id'),$reviewModel->getData('detail_id'));
            if(!empty($data)){
                $customId = $data['custom_id'];
                $data['photo'] = $this->_photoReviewCollection->create()->addFieldToFilter('custom_review_detail_id', $customId)->getItems();
            }
        }
        return $data;
    }
    public function getMediaUrl($path){
        return $this->_photoUpload->getBaseUrl().$path;
    }
}