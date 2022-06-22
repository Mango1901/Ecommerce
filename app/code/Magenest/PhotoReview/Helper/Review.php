<?php

namespace Magenest\PhotoReview\Helper;

use Exception;
use Magenest\PhotoReview\Model\Photo\Uploader;
use Magenest\PhotoReview\Model\ResourceModel\Photo\CollectionFactory;
use Magenest\PhotoReview\Model\Session as ReviewSession;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\UrlInterface;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory as MagentoCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magenest\PhotoReview\Model\Video\Uploader as VideoUploader;

class Review extends AbstractHelper
{
    const ATTRIBUTE_START = [
        '5_start' => 5,
        '4_start' => 4,
        '3_start' => 3,
        '2_start' => 2,
        '1_start' => 1,
    ];

    const ATTRIBUTE_ONLY_IMAGE = 'only-image';

    /** @var ProductRepositoryInterface */
    protected $_productRepository;

    /** @var Data */
    protected $_helperData;

    /** @var CollectionFactory */
    protected $_photoReviewCollection;

    /** @var Uploader */
    protected $_photoUpload;

    /** @var Session */
    protected $_customerSession;

    /**
     * Url Builder
     *
     * @var UrlInterface
     */
    protected $_urlBuilder;
    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * Store manager
     *
     * @var MagentoCollectionFactory
     */
    protected $magentoCollectionFactory;
    /** @var ResourceConnection */
    protected $_resource;
    /**
     * @var SerializerInterface
     */
    private $serialize;
    /**
     * @var ReviewSession
     */
    private $_sessionPhotoReview;

    /**
     * @var VideoUploader
     */
    private $videoUpload;

    /**
     * Review constructor.
     *
     * @param VideoUploader $videoUpload
     * @param ProductRepositoryInterface $productRepository
     * @param Data $helperData
     * @param CollectionFactory $collectionFactory
     * @param Uploader $photoUpload
     * @param Session $customerSession
     * @param Context $context
     * @param UrlInterface $urlBuilder
     * @param SerializerInterface $serialize
     * @param ReviewSession $sessionPhotoReview
     * @param StoreManagerInterface $storeManager
     * @param MagentoCollectionFactory $magentoCollectionFactory
     * @param ResourceConnection $_resource
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        Data                       $helperData,
        CollectionFactory          $collectionFactory,
        Uploader                   $photoUpload,
        Session                    $customerSession,
        Context                    $context,
        UrlInterface               $urlBuilder,
        SerializerInterface        $serialize,
        ReviewSession              $sessionPhotoReview,
        StoreManagerInterface      $storeManager,
        MagentoCollectionFactory   $magentoCollectionFactory,
        ResourceConnection         $_resource,
        VideoUploader              $videoUpload
    )
    {
        $this->_productRepository = $productRepository;
        $this->_helperData = $helperData;
        $this->_photoReviewCollection = $collectionFactory;
        $this->_photoUpload = $photoUpload;
        $this->_customerSession = $customerSession;
        $this->_urlBuilder = $urlBuilder;
        $this->serialize = $serialize;
        $this->_sessionPhotoReview = $sessionPhotoReview;
        $this->_storeManager = $storeManager;
        $this->magentoCollectionFactory = $magentoCollectionFactory;
        $this->_resource = $_resource;
        $this->_videoUpload = $videoUpload;
        parent::__construct($context);
    }

    /**
     * @param $reviewModel
     * @return array
     * @throws NoSuchEntityException
     */
    public function getCustomReviewDetail($reviewModel)
    {
        $data = null;
        if ($reviewModel->getData('review_id') && $reviewModel->getData('detail_id')) {
            $data = $this->_helperData->getCustomReviewDetail($reviewModel->getData('review_id'));
            $data['display_admin_comment'] = false;
            if (isset($data['admin_comment'])) {
                $data['display_admin_comment'] = (boolean)$this->_helperData->getScopeConfig(Data::ENABLE_ADMIN_COMMENT);
            }
            if (!empty($data) && isset($data['review_id'])) {
                $reviewId = $data['review_id'];
                $data['photo'] = $this->_photoReviewCollection->create()->addFieldToFilter('review_id', $reviewId)->getItems();
            }
            $productId = $reviewModel->getData('entity_pk_value');
            /** @var Product $product */
            $product = $this->_productRepository->getById($productId);
            if ($product->getId()) {
                $data['product_name'] = $product->getName();
                $data['product_link'] = $product->getProductUrl();
            }
        }
        return $data;
    }

    /**
     * @param $path
     * @return string
     */
    public function getMediaUrl($path, $type = null)
    {
        if ($type == \Magenest\PhotoReview\Observer\Review\Detail::TYPE_VIDEO) {
            return $this->_videoUpload->getBaseUrl() . $path;
        }  else {
            return $this->_photoUpload->getBaseUrl() . $path;
        }
    }


    /**
     * @return mixed
     */
    public function getAppId()
    {
        return $this->_helperData->getScopeConfig(Data::APP_ID);
    }

    /**
     * Return review url
     *
     * @param int $id
     * @return string
     */
    public function getReviewUrl($id)
    {
        return $this->_urlBuilder->getUrl('review/product/view', ['id' => $id]);
    }

    /**
     * Get Label Summary Options
     *
     * @param string|null $summary
     * @return array|bool
     */
    public function getLabelSummary($summary)
    {
        $result = [];
        try {
            $summaryArray = explode(',', $summary);
            $summaryOptions = $this->_helperData->getLabelSummary();
            if (is_array($summaryArray)) {
                foreach ($summaryArray as $item) {
                    foreach ($summaryOptions as $summaryOption) {
                        if ($item === $summaryOption['value']) {
                            $result[] = $summaryOption['label'];
                        }
                    }
                }
            } else {
                $result[] = $summary;
            }

        } catch (Exception $e) {
            $this->_logger->error($e->getMessage());
        }
        return $result;
    }

    /**
     * Used for remove session filter saved in previous request
     */
    public function removeSessionFilter()
    {
        $this->_sessionPhotoReview->setData('start', null);
        $this->_sessionPhotoReview->setData('onlyImage', null);
    }

    /**
     * Count Number Review of each filter
     *
     * @param $product
     * @return array
     * @throws NoSuchEntityException
     */
    public function getCountReviewFilter($product)
    {
        $result = [];

        if ($product && ($id = $product->getId())) {
            $generalCollection = $this->magentoCollectionFactory->create()->addStoreFilter(
                $this->_storeManager->getStore()->getId()
            )->addStatusFilter(
                \Magento\Review\Model\Review::STATUS_APPROVED
            )->addEntityFilter(
                'product',
                $id
            );

            $ratingOptionVote = $this->_resource->getTableName('rating_option_vote');
            $photoReview = $this->_resource->getTableName('magenest_photoreview_photo');

            foreach (self::ATTRIBUTE_START as $key => $value) {
                $startCollection = clone $generalCollection;
                $startCollection->getSelect()->join(
                    ['ratingVote' => $ratingOptionVote],
                    'ratingVote.review_id = main_table.review_id',
                    'value'
                )->where("ratingVote.value = ?", $value);
                $result[$key] = $startCollection->count();
            }

            $imageCollection = clone $generalCollection;
            $imageCollection->getSelect()->join(
                ['photoreview' => $photoReview],
                'main_table.review_id = photoreview.review_id',
                []
            )->distinct(true);
            $result[self::ATTRIBUTE_ONLY_IMAGE] = $imageCollection->count();
        }

        return $result;
    }
}
