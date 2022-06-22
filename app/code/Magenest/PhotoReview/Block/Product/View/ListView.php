<?php
namespace Magenest\PhotoReview\Block\Product\View;

use Magento\Catalog\Api\ProductRepositoryInterface;

class ListView extends \Magento\Review\Block\Product\View\ListView
{
    /** @var \Magenest\PhotoReview\Model\Session  */
    protected $_sessionPhotoReview;

    /** @var \Magento\Framework\App\ResourceConnection  */
    protected $_resource;

    /** @var \Magenest\PhotoReview\Model\ResourceModel\ReviewDetail */
    protected $_photoReviewResource;

    /**
     * ListView constructor.
     *
     * @param \Magenest\PhotoReview\Model\Session $sessionPhotoReview
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magenest\PhotoReview\Model\ResourceModel\ReviewDetail $reviewDetail
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Customer\Model\Session $customerSession
     * @param ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Review\Model\ResourceModel\Review\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magenest\PhotoReview\Model\Session $sessionPhotoReview,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magenest\PhotoReview\Model\ResourceModel\ReviewDetail $reviewDetail,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $collectionFactory,
        array $data = []
    ){
        $this->_sessionPhotoReview = $sessionPhotoReview;
        $this->_resource = $resource;
        $this->_photoReviewResource = $reviewDetail;
        parent::__construct($context, $urlEncoder, $jsonEncoder, $string, $productHelper, $productTypeConfig, $localeFormat, $customerSession, $productRepository, $priceCurrency, $collectionFactory, $data);
    }

    /**
     * @return $this|\Magento\Review\Block\Product\View\ListView
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $toolbar = $this->getLayout()->getBlock('product_review_list.toolbar');
        if ($toolbar) {
            $toolbar->setCollection($this->getReviewsCollection());
            $this->setChild('toolbar', $toolbar);
        }
        return $this;
    }

    /**
     * @return \Magento\Review\Model\ResourceModel\Review\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getReviewsCollection()
    {
        if($this->_reviewsCollection === null){
            $this->_reviewsCollection = $this->_reviewsColFactory->create()->addStoreFilter(
                $this->_storeManager->getStore()->getId()
            )->addStatusFilter(
                \Magento\Review\Model\Review::STATUS_APPROVED
            )->addEntityFilter(
                'product',
                $this->getProduct()->getId()
            );

            $reviewsCollection = clone $this->_reviewsCollection->setDateOrder();
            $value = $this->_sessionPhotoReview->getData('start');
            $onlyImage = $this->_sessionPhotoReview->getData('onlyImage');
            if ($value) {
                $ratingOptionVote = $this->_resource->getTableName('rating_option_vote');
                $reviewsCollection->getSelect()->join(
                    ['ratingVote' => $ratingOptionVote],
                    'ratingVote.review_id = main_table.review_id',
                    'value'
                )->where("ratingVote.value = ?", $value);
                $this->_reviewsCollection = $reviewsCollection;
            }

            if ($onlyImage) {
                $reviewsCollection = $this->addFilterPhoto($this->_reviewsCollection);
                $this->_reviewsCollection = $reviewsCollection;
            }
        }

        return $this->_reviewsCollection;
    }

    /**
     * @param $reviewsCollection
     * @return mixed
     */
    protected function addFilterPhoto($reviewsCollection)
    {
        $photoReview = $this->_resource->getTableName('magenest_photoreview_photo');
        $reviewsCollection->getSelect()->join(
            ['photoreview' => $photoReview],
            'main_table.review_id = photoreview.review_id',
            []
        )->distinct(true);
        return $reviewsCollection;
    }

    /**
     * @param $reviewsCollection
     * @return mixed
     */
    protected function addFilterNotPhoto($reviewsCollection)
    {
        $photoReview = $this->_resource->getTableName('magenest_photoreview_photo');

        $reviewsCollection->getSelect()->where(
            'main_table.review_id NOT IN (?)',
            $this->_resource->getConnection()
                ->select()
                ->from(
                    $photoReview,
                    ['review_id']
                )
        )->distinct(true);
        return $reviewsCollection;
    }
}
