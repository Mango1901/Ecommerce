<?php

namespace Magenest\PhotoReview\Controller\Product;

use Magenest\PhotoReview\Model\Session;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Design;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Registry;
use Magento\Framework\Session\Generic;
use Magento\Framework\View\Result\Layout;
use Magento\Review\Controller\Product as ProductController;
use Magento\Review\Model\RatingFactory;
use Magento\Review\Model\ReviewFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class ListAjax extends ProductController
{
    /**
     * @var Session
     */
    protected $_sessionPhotoReview;

    public function __construct(
        Session                         $sessionPhotoReview,
        Context                         $context,
        Registry                        $coreRegistry,
        \Magento\Customer\Model\Session $customerSession,
        CategoryRepositoryInterface     $categoryRepository,
        LoggerInterface                 $logger,
        ProductRepositoryInterface      $productRepository,
        ReviewFactory                   $reviewFactory,
        RatingFactory                   $ratingFactory,
        Design                          $catalogDesign,
        Generic                         $reviewSession,
        StoreManagerInterface       $storeManager,
        Validator   $formKeyValidator
    ) {
        $this->_sessionPhotoReview = $sessionPhotoReview;
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
     * Show list of product's reviews
     *
     * @return ResponseInterface|ResultInterface|Layout
     */
    public function execute()
    {
        if (!$this->initProduct()) {
            /** @var Forward $resultForward */
            $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            return $resultForward->forward('noroute');
        }
        $params = $this->getRequest()->getParams();

        if (isset($params['value'])) {
            $valueFilter = $params['value'];
            $this->_sessionPhotoReview->setData('p', $params['p']);

            if (strpos($valueFilter, 'start') !== false) {
                $startFilter = (int)filter_var($valueFilter, FILTER_SANITIZE_NUMBER_INT);
                $this->_sessionPhotoReview->setData('start', $startFilter);
            }

            if ($valueFilter === 'only-image') {
                $this->_sessionPhotoReview->setData('onlyImage', true);
            }
        }

        return $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
    }
}
