<?php

namespace Magenest\PhotoReview\Controller\Product;

use Exception;
use Magenest\PhotoReview\Helper\Data;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Design;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Session\Generic;
use Magento\Review\Model\RatingFactory;
use Magento\Review\Model\Review;
use Magento\Review\Model\ReviewFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Post extends \Magento\Review\Controller\Product\Post
{
    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * @var SerializerInterface
     */
    private $serialize;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Session $customerSession
     * @param CategoryRepositoryInterface $categoryRepository
     * @param LoggerInterface $logger
     * @param ProductRepositoryInterface $productRepository
     * @param ReviewFactory $reviewFactory
     * @param RatingFactory $ratingFactory
     * @param Design $catalogDesign
     * @param Generic $reviewSession
     * @param StoreManagerInterface $storeManager
     * @param Validator $formKeyValidator
     * @param Data $dataHelper
     * @param SerializerInterface $serialize
     */
    public function __construct(
        Context                     $context,
        Registry                    $coreRegistry,
        Session                     $customerSession,
        CategoryRepositoryInterface $categoryRepository,
        LoggerInterface             $logger,
        ProductRepositoryInterface  $productRepository,
        ReviewFactory               $reviewFactory,
        RatingFactory               $ratingFactory,
        Design                      $catalogDesign,
        Generic                     $reviewSession,
        StoreManagerInterface       $storeManager,
        Validator                   $formKeyValidator,
        Data                        $dataHelper,
        SerializerInterface         $serialize
    ) {
        $this->dataHelper = $dataHelper;
        $this->serialize = $serialize;
        parent::__construct(
            $context,
            $coreRegistry,
            $customerSession,
            $categoryRepository,
            $logger,
            $productRepository,
            $reviewFactory,
            $ratingFactory,
            $catalogDesign,
            $reviewSession,
            $storeManager,
            $formKeyValidator
        );
    }

    /**
     * Submit new review action
     *
     * @return Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }

        $data = $this->reviewSession->getFormData(true);
        if ($data) {
            $rating = [];
            if (isset($data['ratings']) && is_array($data['ratings'])) {
                $rating = $data['ratings'];
            }
        } else {
            $data = $this->getRequest()->getPostValue();
            $rating = $this->getRequest()->getParam('ratings', []);
        }

        if (isset($data['title']) && is_array($data['title'])) {
            $data['title'] = implode(',', array_keys($data['title']));
        }

        if (($product = $this->initProduct()) && !empty($data)) {
            /** @var Review $review */
            $review = $this->reviewFactory->create()->setData($data);
            $review->unsetData('review_id');

            $validate = $this->validate($review);
            if ($validate === true) {
                try {
                    if ($this->dataHelper->isEnableAutoApproved()) {
                        $review->setStatusId(Review::STATUS_APPROVED);
                    } else {
                        $review->setStatusId(Review::STATUS_PENDING);
                    }

                    $review->setEntityId($review->getEntityIdByCode(Review::ENTITY_PRODUCT_CODE))
                        ->setEntityPkValue($product->getId())
                        ->setCustomerId($this->customerSession->getCustomerId())
                        ->setStoreId($this->storeManager->getStore()->getId())
                        ->setStores([$this->storeManager->getStore()->getId()])
                        ->save();

                    foreach ($rating as $ratingId => $optionId) {
                        $this->ratingFactory->create()
                            ->setRatingId($ratingId)
                            ->setReviewId($review->getId())
                            ->setCustomerId($this->customerSession->getCustomerId())
                            ->addOptionVote($optionId, $product->getId());
                    }

                    $review->aggregate();

                    if ($this->dataHelper->isEnableAutoApproved()) {
                        $this->messageManager->addSuccessMessage(__('Thank you for rating!'));
                    } else {
                        $this->messageManager->addSuccessMessage(__('You submitted your review for moderation.'));
                    }

                } catch (Exception $e) {
                    $this->reviewSession->setFormData($data);
                    $this->messageManager->addErrorMessage(__('We can\'t post your review right now.'));
                }
            } else {
                $this->reviewSession->setFormData($data);
                if (is_array($validate)) {
                    foreach ($validate as $errorMessage) {
                        $this->messageManager->addErrorMessage($errorMessage);
                    }
                } else {
                    $this->messageManager->addErrorMessage(__('We can\'t post your review right now.'));
                }
            }
        }
        $redirectUrl = $this->reviewSession->getRedirectUrl(true);
        $resultRedirect->setUrl($redirectUrl ?: $this->_redirect->getRedirectUrl());
        return $resultRedirect;
    }

    /**
     * Validate review summary fields
     * @param $review
     * @return bool|string[]
     * @throws \Zend_Validate_Exception
     */
    private function validate($review)
    {
        $errors = [];

        if (!\Zend_Validate::is($review->getNickname(), 'NotEmpty')) {
            $errors[] = __('Please enter a nickname.');
        }

        if (!\Zend_Validate::is($review->getDetail(), 'NotEmpty')) {
            $errors[] = __('Please enter a review.');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }
}
