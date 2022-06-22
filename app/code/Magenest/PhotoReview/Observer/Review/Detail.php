<?php
namespace Magenest\PhotoReview\Observer\Review;

use Magenest\PhotoReview\Model\Photo\Uploader;
use Magenest\PhotoReview\Model\PhotoFactory;
use Magenest\PhotoReview\Model\ResourceModel\Photo;
use Magenest\PhotoReview\Model\ResourceModel\ReviewDetail\CollectionFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magenest\PhotoReview\Helper\Data;
use Magenest\PhotoReview\Model\Video\Uploader as UploaderVideo;

class Detail implements ObserverInterface
{
    const TYPE_IMAGE = 'image';
    const TYPE_VIDEO = 'video';

    /** @var \Magenest\PhotoReview\Helper\Data  */
    protected $_helperData;

    /** @var PhotoFactory $_photoReviewFactory */
    protected $_photoReviewFactory;

    /** @var Photo $_photoReviewResource */
    protected $_photoReviewResource;

    /** @var Uploader $_photoUpload */
    protected $_photoUpload;

    /** @var UploaderFactory $_uploaderFactory */
    protected $_uploaderFactory;

    /** @var \Magento\Review\Model\ResourceModel\Review $_reviewResource */
    protected $_reviewResource;

    /** @var \Magento\Review\Model\ResourceModel\Review\CollectionFactory $_reviewCollection */
    protected $_reviewCollection;

    /** @var \Magento\Framework\App\RequestInterface $_request */
    protected $_request;

    /** @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory  */
    protected $_orderCollection;

    /** @var \Magento\Framework\App\ResourceConnection  */
    protected $_resource;

    /** @var \Magenest\PhotoReview\Model\SendCouponCron  */
    protected $_sendCouponModel;

    /** @var \Magenest\PhotoReview\Model\ReviewDetailFactory  */
    protected $_reviewDetailFactory;

    /** @var \Magenest\PhotoReview\Model\ResourceModel\ReviewDetail  */
    protected $_reviewDetailResource;

    /** @var CollectionFactory  */
    protected $_reviewDetailCollection;

    /** @var \Psr\Log\LoggerInterface  */
    protected $_logger;

    /**
     * @var UploaderVideo
     */
    protected $videoUpload;

    /**
     * Detail constructor.
     *
     * @param Data $helperData
     * @param PhotoFactory $photoFactory
     * @param Photo $photoResource
     * @param Uploader $photoUpload
     * @param UploaderVideo $videoUpload
     * @param UploaderFactory $uploaderFactory
     * @param \Magento\Review\Model\ResourceModel\Review $reviewResource
     * @param \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollection
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magenest\PhotoReview\Model\SendCouponCron $sendCouponCron
     * @param \Magenest\PhotoReview\Model\ReviewDetailFactory $reviewDetailFactory
     * @param \Magenest\PhotoReview\Model\ResourceModel\ReviewDetail $reviewDetail
     * @param CollectionFactory $reviewDetailCollection
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magenest\PhotoReview\Helper\Data $helperData,
        \Magenest\PhotoReview\Model\PhotoFactory $photoFactory,
        \Magenest\PhotoReview\Model\ResourceModel\Photo $photoResource,
        \Magenest\PhotoReview\Model\Photo\Uploader $photoUpload,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Review\Model\ResourceModel\Review $reviewResource,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollection,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magenest\PhotoReview\Model\SendCouponCron $sendCouponCron,
        \Magenest\PhotoReview\Model\ReviewDetailFactory $reviewDetailFactory,
        \Magenest\PhotoReview\Model\ResourceModel\ReviewDetail $reviewDetail,
        \Magenest\PhotoReview\Model\ResourceModel\ReviewDetail\CollectionFactory $reviewDetailCollection,
        \Psr\Log\LoggerInterface $logger,
        UploaderVideo $videoUpload
    ){
        $this->_helperData = $helperData;
        $this->_photoReviewFactory = $photoFactory;
        $this->_photoReviewResource = $photoResource;
        $this->_photoUpload = $photoUpload;
        $this->_uploaderFactory = $uploaderFactory;
        $this->_reviewResource = $reviewResource;
        $this->_reviewCollection = $reviewCollection;
        $this->_request = $request;
        $this->_orderCollection = $orderCollection;
        $this->_resource = $resource;
        $this->_sendCouponModel = $sendCouponCron;
        $this->_reviewDetailFactory = $reviewDetailFactory;
        $this->_reviewDetailResource = $reviewDetail;
        $this->_reviewDetailCollection = $reviewDetailCollection;
        $this->_logger = $logger;
        $this->_videoUpload = $videoUpload;
    }

    public function execute(Observer $observer)
    {
        $isModuleEnable = $this->_helperData->isModuleEnable();
        $params = $this->_request->getParams();
        $flag = true;
        if(isset($params['massaction_prepare_key'])){
            $flag = false;
        }
        if($flag){
            /** @var \Magento\Review\Model\Review $reviewModel */
            $reviewModel = $observer->getEvent()->getData('object');
            if($reviewModel instanceof \Magento\Review\Model\Review ){
                try{
                    /** @var  \Magento\Review\Model\Review $reviewModel */
                    $reviewId = $reviewModel->getReviewId();
                    $storeId = $reviewModel->getStoreId();
                    $connect = $this->_resource->getConnection();
                    $reviewDetailTable = $this->_resource->getTableName('review_detail');
                    $select = $connect->select()->from($reviewDetailTable, 'detail_id')->where('review_id = :review_id');
                    $detailId = $connect->fetchCol($select, [':review_id' => $reviewId]);
                    $pros = $reviewModel->getData('photo_review_pros');
                    $cons = $reviewModel->getData('photo_review_cons');
                    $externalLinks = $reviewModel->getData('photo_external_links');
                    $isRecommend = (boolean)$reviewModel->getData('photo_review_is_recommend');
                    $data = [
                        "photo_review_pros" => $pros,
                        "photo_review_cons" => $cons,
                        "photo_review_is_recommend" => $isRecommend,
                        "photo_external_links" => $externalLinks
                    ];
                    $adminComment = $reviewModel->getData('admin_comment');
                    $customReviewDetailTable = $this->_resource->getTableName('magenest_photoreview_detail');
                    $reviewDetailModel = $this->_reviewDetailFactory->create();
                    $this->_reviewDetailResource->load($reviewDetailModel,$reviewId,'review_id');
                    if(($adminComment||$adminComment!='')||(!empty($detailId) && $reviewDetailModel->getCustomId())){
                        $data['admin_comment'] = $adminComment;
                        $data["review_id"] = $reviewId;
                        $condition = ["review_id = ?" => $reviewId];
                        $connect->update($customReviewDetailTable, $data, $condition);
                    }elseif(!empty($detailId)){
                        //Is data assigned to product?
                        $entityId = $reviewModel->getData('entity_id');
                        $reviewEntityTbl = $this->_resource->getTableName('review_entity');
                        $reviewEntityQuery = $connect->select()->from($reviewEntityTbl, 'entity_code')->where("entity_id = ?",$entityId);
                        $entityCode = $connect->fetchOne($reviewEntityQuery);
                        $isPurchased = 0;
                        if($reviewModel->getData('customer_id') != null && $entityCode == 'product'){
                            $customerId = $reviewModel->getData('customer_id');
                            $productId = $reviewModel->getData('entity_pk_value');
                            $isPurchased = $this->isPurchased($customerId,$storeId,$productId);
                            if($isPurchased != 0 && $this->checkSendCoupon($productId,$isPurchased)){
                                $data["order_id"] = $isPurchased;
                                $customer = $this->_sendCouponModel->getCustomerById($customerId);
                                $this->_sendCouponModel->cancelReminderEmail($customer->getEmail(), $productId);
                                $this->_sendCouponModel->sendCoupon($reviewModel,$productId,$customerId);
                            }
                        }
                        $data["review_id"] = $reviewId;
                        $data["is_purchased"] = $isPurchased == 0 ? 0 : 1;
                        $connect->insert($customReviewDetailTable, $data);
                    }
                    $isRequiredImages = (boolean)$this->_helperData->getScopeConfig(Data::REQUIRED_PHOTO);
                    $images = $this->_request->getFiles('photo');
                    $videos = $this->_request->getFiles('video');
                    $data = [];
                    $dataVideo = [];
                    if($images&&!empty($detailId)){
                        foreach ($images as $image){
                            if(empty($image['tmp_name']) && $isRequiredImages){
                                continue;
                            }else{
                                $path = $this->uploadAndGetName($image, $this->_photoUpload->getBaseDir(), $images);
                                if($path !== ""){
                                    $data[] = [
                                        "review_id" => $reviewId,
                                        "path" => $path,
                                        "store_id" => $storeId,
                                        'type' => self::TYPE_IMAGE
                                    ];
                                }
                            }
                        }
                        if(!empty($data)){
                            $this->_photoReviewResource->insertMuiltiRecord($data);
                        }
                    }

                    if(!empty($videos)){
                        if($videos&&!empty($detailId)){
                            foreach ($videos as $video){
                                if(empty($video['tmp_name'])){
                                    continue;
                                }else{
                                    $path = $this->uploadAndGetNameVideo($video, $this->_videoUpload->getBaseDir(), $videos);
                                    if($path !== ""){
                                        $dataVideo[] = [
                                            "review_id" => $reviewId,
                                            "path" => $path,
                                            "store_id" => $storeId,
                                            'type' => self::TYPE_VIDEO
                                        ];
                                    }
                                }
                            }
                            if(!empty($dataVideo)){
                                $this->_photoReviewResource->insertMuiltiRecord($dataVideo);
                            }
                        }
                    }

                }catch (\Exception $exception){
                    $this->_logger->critical($exception->getMessage());
                }
            }
        }
    }

    /**
     * @param $input
     * @param $destinationFolder
     * @param $data
     *
     * @return string
     * @throws \Exception
     */
    private function uploadAndGetName($input, $destinationFolder, $data){
        try{
            if(is_array($input)&&isset($input['name'])&& !empty($input['name'])){
                $uploader = $this->_uploaderFactory->create(['fileId' => $input]);
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(true);
                $uploader->setAllowCreateFolders(true);
                $result = $uploader->save($destinationFolder);
                return $result['file'];
            }else{
                return "";
            }
        }catch (\Exception $e){
            return "";
        }
    }

    private function uploadAndGetNameVideo($input, $destinationFolder, $data){
        try{
            if(is_array($input)&&isset($input['name'])&& !empty($input['name'])){
                $uploader = $this->_uploaderFactory->create(['fileId' => $input]);
                $uploader->setAllowedExtensions(['mp4']);
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(true);
                $uploader->setAllowCreateFolders(true);
                $result = $uploader->save($destinationFolder);
                return $result['file'];
            }else{
                return "";
            }
        }catch (\Exception $e){
            return "";
        }
    }


    private function isPurchased($customerId, $storeId, $productId)
    {
        $collections = $this->_orderCollection->create()
            ->addFieldToFilter("customer_id", $customerId)
            ->addFieldToFilter("store_id", $storeId)
            ->setOrder('created_at', 'desc')
            ->setOrder('entity_id', 'desc')
            ->getItems();
        $flag = 0;
        /** @var \Magento\Sales\Model\Order $order */
        foreach ($collections as $order){
            /** @var \Magento\Sales\Model\Order\Item $item */
            foreach ($order->getAllVisibleItems() as $item) {
                $itemProductId = $item->getProduct()->getId();
                if($itemProductId == $productId){
                    $flag = $order->getEntityId();
                    break 2;
                }
            }
        }
        return $flag;
    }

    /**
     * @param $reviewId
     * @param $orderId
     *
     * @return bool
     */
    private function checkSendCoupon($productId,$orderId)
    {
        $collections = $this->_reviewDetailCollection->create()
            ->addFieldToFilter('order_id', $orderId);
        /** @var \Magenest\PhotoReview\Model\ReviewDetail $reviewDetail */
        foreach ($collections as $reviewDetail){
            if($reviewDetail->getCustomId()){
                $id = $this->getProductIdByReviewId($reviewDetail->getReviewId());
                if($id == $productId){
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @param $reviewId
     *
     * @return array
     */
    private function getProductIdByReviewId($reviewId)
    {
        $connect = $this->_resource->getConnection();
        $reviewTable = $this->_resource->getTableName('review');
        $select = $connect->select()->from($reviewTable, 'entity_pk_value')->where('review_id = :review_id');
        $productId = $connect->fetchOne($select, [':review_id' => $reviewId]);
        return $productId;
    }
}
