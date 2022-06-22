<?php

namespace Magenest\PhotoReview\Block\Review\View;

class Photo extends \Magento\Framework\View\Element\Template
{
    /** @var \Magento\Catalog\Api\ProductRepositoryInterface  */
    protected $_productRepository;

    /** @var \Magenest\PhotoReview\Helper\Review  */
    protected $_reviewHelper;

    /** @var \Magenest\PhotoReview\Model\ResourceModel\Photo\CollectionFactory  */
    protected $_photoReviewCollection;

    /** @var \Magenest\PhotoReview\Helper\Data  */
    protected $_helperData;

    /** @var \Magenest\PhotoReview\Model\Session */
    protected $_sessionPhotoReview;

    /** @var \Magento\Review\Model\ReviewFactory */
    protected $_reviewFactory;

    /** @var \Magento\Review\Model\ResourceModel\Review */
    protected $_reviewResource;

    /** @var \Psr\Log\LoggerInterface */
    protected $_logger;

    /** @var \Magento\Review\Model\ResourceModel\Review\CollectionFactory  */
    protected $_reviewsColFactory;

    /**
     * Photo constructor.
     *
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magenest\PhotoReview\Helper\Review $reviewHelper
     * @param \Magenest\PhotoReview\Model\ResourceModel\Photo\CollectionFactory $photoReviewCollection
     * @param \Magenest\PhotoReview\Helper\Data $helperData
     * @param \Magenest\PhotoReview\Model\Session $sessionPhotoReview
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param \Magento\Review\Model\ResourceModel\Review $reviewResource
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollection
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magenest\PhotoReview\Helper\Review $reviewHelper,
        \Magenest\PhotoReview\Model\ResourceModel\Photo\CollectionFactory $photoReviewCollection,
        \Magenest\PhotoReview\Helper\Data $helperData,
        \Magenest\PhotoReview\Model\Session $sessionPhotoReview,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Review\Model\ResourceModel\Review $reviewResource,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollection,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->_productRepository = $productRepository;
        $this->_reviewHelper = $reviewHelper;
        $this->_photoReviewCollection = $photoReviewCollection;
        $this->_helperData = $helperData;
        $this->_sessionPhotoReview = $sessionPhotoReview;
        $this->_reviewFactory      = $reviewFactory;
        $this->_reviewResource     = $reviewResource;
        $this->_logger             = $logger;
        $this->_reviewsColFactory = $reviewCollection;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Review\Model\Review|null
     */
    public function getPhotoReview()
    {
        $reviewId = $this->_sessionPhotoReview->getData('reviewId');
        $photoId = $this->_sessionPhotoReview->getData('photoId');
        $result = null;
        if($reviewId != null && $photoId != null){
            try{
                /** @var \Magento\Review\Model\Review $reviewModel */
                $reviewModel = $this->_reviewsColFactory->create()->addFieldToFilter(
                    'main_table.review_id',
                    $reviewId
                )->addStoreFilter(
                    $this->_storeManager->getStore()->getId()
                )->addStatusFilter(
                    \Magento\Review\Model\Review::STATUS_APPROVED
                )->addRateVotes()->getFirstItem();
                if(!$reviewModel->getReviewId()){
                    throw new \Exception(__("The review does not exist! Id: %1", $reviewId));
                }
                $result = $reviewModel;
            }catch (\Exception $exception){
                $this->_logger->critical($exception->getMessage());
            }
        }
        return $result;
    }
    public function getPhotoData($reviewModel)
    {
        $result = $reviewModel->getData();
        $customData = $this->_helperData->getCustomReviewDetail($result['review_id']);
        if(!empty($customData)){
            $reviewId = $customData['review_id'];
            $photos = $this->_photoReviewCollection->create()->addFieldToFilter('review_id', $reviewId)->getItems();
            $images = [];
            $photoId = $this->_sessionPhotoReview->getData('photoId');
            $index = $i = 0;
            foreach ($photos as $photo){
                if($photo->getData('path') != ''){
                    if($photo->getData('photo_id') == $photoId) $index = $i;
                    $type =  $photo->getData('type');
                    $url = $this->_reviewHelper->getMediaUrl($photo->getData('path'),$type);
                    $images[$i]['url'] = $url;
                    $images[$i]['type'] = $type;
                    $i++;
                }
            }
            if(!empty($images)){
                $this->moveElement($images, $index);
            }
            $customData['photos'] = $images;
        }
        $result['additional_data'] = $customData;
        $productId = $result['entity_pk_value'];
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->_productRepository->getById($productId);
        if($product->getId()){
            $result['additional_data']['product_name'] = $product->getName();
            $result['additional_data']['product_link'] = $product->getProductUrl();
            try{
                $result['additional_data']['product_image'] = $product->getMediaGalleryImages()->getFirstItem()->getUrl();
            }catch (\Exception $exception){
                $this->_logger->critical($exception->getMessage());
            }
        }
        return $result;
    }
    public function moveElement(&$array, $a, $b = 0) {
        $out = array_splice($array, $a, 1);
        array_splice($array, $b, 0, $out);
    }
    /**
     * Return review url
     *
     * @param int $id
     * @return string
     */
    public function getReviewUrl($id)
    {
        return $this->getUrl('review/product/view', ['id' => $id]);
    }
}
