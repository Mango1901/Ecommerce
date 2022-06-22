<?php
namespace Magenest\MobileApi\Model\Review;

use Magenest\MobileApi\Api\Data\ReviewInterface as ReviewData;
use Magenest\MobileApi\Api\Data\ReviewInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Review\Model\ResourceModel\Review as ReviewResource;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Review\Model\Review;
use Magento\Review\Model\ReviewFactory;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magenest\MobileApi\Api\Data\ReviewInterfaceFactory;
use Magento\Framework\DataObject\Copy as ObjectCopyService;
use Magento\Store\Model\Store;
use Psr\Log\LoggerInterface;

class Save
{
    /** @var ReviewResource */
    private $reviewResource;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var ReviewFactory */
    private $reviewFactory;

    /** @var DataObjectProcessor */
    private $dataObjectProcessor;

    /** @var ReviewInterfaceFactory */
    private $reviewInterfaceFactory;

    /** @var ObjectCopyService */
    private $objectCopyService;

    /** @var LoggerInterface  */
    protected $_logger;

    /**
     * Save constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param ReviewResource $reviewResource
     * @param ReviewFactory $reviewFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param ReviewInterfaceFactory $reviewInterfaceFactory
     * @param ObjectCopyService $objectCopyService
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ReviewResource $reviewResource,
        ReviewFactory $reviewFactory,
        DataObjectProcessor $dataObjectProcessor,
        ReviewInterfaceFactory $reviewInterfaceFactory,
        ObjectCopyService $objectCopyService,
        LoggerInterface $logger
    ) {
        $this->reviewResource = $reviewResource;
        $this->storeManager = $storeManager;
        $this->reviewFactory = $reviewFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->objectCopyService = $objectCopyService;
        $this->reviewInterfaceFactory = $reviewInterfaceFactory;
        $this->_logger = $logger;
    }

    /**
     * Save Review
     *
     * @param ReviewInterface $dataModel
     *
     * @return ReviewInterface|array
     * @throws AlreadyExistsException
     */
    public function execute(ReviewInterface $dataModel)
    {
        return $this->saveReview($dataModel);
    }

    /**
     * Save Review
     *
     * @param ReviewInterface $dataModel
     *
     * @return ReviewInterface|array
     * @throws AlreadyExistsException
     */
    private function saveReview(ReviewInterface $dataModel) {
        if ($dataModel->getId()) {
            /** @var Review $reviewModel */
            $model = $this->reviewFactory->create();
            try {
                $this->resourceModel->load($model, $dataModel->getId());
            } catch (\Exception $exception) {
                $this->_logger->debug(new NoSuchEntityException(
                    __('Review with id "%value" does not exist.', ['value' => $dataModel->getId()])
                ));

                return [];
            }

        } else {
            $model = $this->reviewFactory->create();
            $mergedData = $this->mergeReviewData($model, $dataModel);
            $model->setData($mergedData);
            $this->mapFields($model, $dataModel);
        }

        $this->reviewResource->save($model);
        $this->reviewResource->load($model, $model->getId());

        if ($dataModel->getStoreId() === null) {
            $dataModel->setStoreId($model->getStoreId());
        }

        $dataModel->setId($model->getId());
        $dataModel->setCreatedAt($model->getCreatedAt());
        $reviewType = ReviewInterface::REVIEW_TYPE_GUEST;
        if ($customerId = $model->getCustomerId()) {
            $reviewType = ReviewInterface::REVIEW_TYPE_CUSTOMER;
        }

        if ((int)$model->getStoreId() === Store::DEFAULT_STORE_ID) {
            $reviewType = ReviewInterface::REVIEW_TYPE_ADMIN;
        }

        $dataModel->setReviewType($reviewType);

        return $dataModel;
    }

    /**
     * Merge Review data from current Review and review data object
     *
     * @param Review $reviewModel
     * @param ReviewData $reviewData
     *
     * @return array
     */
    private function mergeReviewData(Review $reviewModel, ReviewData $reviewData): array
    {
        $data = $this->dataObjectProcessor->buildOutputDataArray(
            $reviewData,
            ReviewInterface::class
        );

        $modelData = $reviewModel->getData();

        return array_merge($modelData, $data);
    }

    /**
     * Map fields from Data Object to Review Model
     *
     * @param Review $reviewModel
     * @param ReviewData $reviewData
     *
     * @return void
     */
    private function mapFields(Review $reviewModel, ReviewData $reviewData): void
    {
        $reviewModel->setEntityId($reviewModel->getEntityIdByCode($reviewData->getReviewEntity()));
        $reviewModel->setStatusId($reviewData->getReviewStatus());
        $reviewModel->setStores($reviewData->getStores());

        if (! $reviewModel->getStatusId()) {
            $reviewModel->setStatusId(Review::STATUS_PENDING);
        }
    }
}
