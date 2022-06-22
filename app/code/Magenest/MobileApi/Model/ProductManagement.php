<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Model;

use Magenest\MobileApi\Api\ProductManagementInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\EntityManager\Operation\Read\ReadExtensions;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Catalog\Model\Layer\FilterList;
use Magento\Framework\App\RequestInterface;
use Magento\Review\Block\Form;
use Magento\Search\Model\QueryFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Product\ProductList\Toolbar;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Search\Model\AutocompleteInterface;
use Magento\Framework\DataObjectFactory;
use Magenest\MobileApi\Api\Data\Catalog\Product\ReviewInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Review\Model\ReviewFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Review\Model\Review;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Review\Model\RatingFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\ProductLinkRepositoryInterface;

/**
 * Class ProductManagement
 * @package Magenest\MobileApi\Model
 */
class ProductManagement implements ProductManagementInterface
{
    /**
     * @var  AutocompleteInterface
     */
    protected $_autoComplete;

    /**
     * @var SearchCriteriaInterface
     */
    protected $_searchCriteria;

    /**
     * @var JoinProcessorInterface
     */
    protected $_extensionAttributesJoinProcessor;

    /**
     * @var ProductSearchResultsInterfaceFactory
     */
    protected $_searchResultsFactory;

    /**
     * @var ReadExtensions
     */
    protected $_readExtensions;

    /**
     * @var LayerResolver
     */
    protected $_layerResolver;

    /**
     * @var FilterList
     */
    protected $_filterList;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var QueryFactory
     */
    protected $_queryFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var DataObjectFactory
     */
    protected $_dataObjectFactory;

    /**
     * @var ProductRepository
     */
    protected $_productRepository;

    /**
     * @var ReviewFactory
     */
    protected $_reviewFactory;

    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * @var RatingFactory
     */
    protected $_ratingFactory;

    /**
     * @var Form
     */
    protected $form;

    /** @var ProductRepositoryInterface */
    protected $_productRepositoryApi;

    /** @var ProductLinkRepositoryInterface */
    protected $_productLinkRepositoryApi;

    /**
     * ProductManagement constructor.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param JoinProcessorInterface $joinProcessor
     * @param ProductSearchResultsInterfaceFactory $productSearchResultsFactory
     * @param LayerResolver $layerResolver
     * @param RequestInterface $request
     * @param FilterList $filterList
     * @param QueryFactory $queryFactory
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param DataObjectFactory $dataObjectFactory
     * @param AutocompleteInterface $autocompleteInterface
     * @param ProductRepository $productRepository
     * @param ReviewFactory $reviewFactory
     * @param CustomerSession $customerSession
     * @param RatingFactory $ratingFactory
     * @param ReadExtensions|null $readExtensions
     * @param Form $form
     * @param ProductRepositoryInterface $_productRepositoryApi
     * @param ProductLinkRepositoryInterface $_productLinkRepositoryApi
     */
    function __construct(
        SearchCriteriaInterface $searchCriteria,
        JoinProcessorInterface $joinProcessor,
        ProductSearchResultsInterfaceFactory $productSearchResultsFactory,
        LayerResolver $layerResolver,
        RequestInterface $request,
        FilterList $filterList,
        QueryFactory $queryFactory,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        DataObjectFactory $dataObjectFactory,
        AutocompleteInterface $autocompleteInterface,
        ProductRepository $productRepository,
        ReviewFactory $reviewFactory,
        CustomerSession $customerSession,
        RatingFactory $ratingFactory,
        ReadExtensions $readExtensions = null,
        Form $form,
        ProductRepositoryInterface $_productRepositoryApi,
        ProductLinkRepositoryInterface $_productLinkRepositoryApi
    )
    {
        $this->_searchCriteria = $searchCriteria;
        $this->_extensionAttributesJoinProcessor = $joinProcessor;
        $this->_searchResultsFactory = $productSearchResultsFactory;
        $this->_readExtensions = $readExtensions ?: ObjectManager::getInstance()->get(ReadExtensions::class);
        $this->_layerResolver = $layerResolver;
        $this->_filterList = $filterList;
        $this->_request = $request;
        $this->_storeManager = $storeManager;
        $this->_queryFactory = $queryFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->_autoComplete = $autocompleteInterface;
        $this->_dataObjectFactory = $dataObjectFactory;
        $this->_productRepository = $productRepository;
        $this->_reviewFactory = $reviewFactory;
        $this->_customerSession = $customerSession;
        $this->_ratingFactory = $ratingFactory;
        $this->form = $form;
        $this->_productRepositoryApi = $_productRepositoryApi;
        $this->_productLinkRepositoryApi = $_productLinkRepositoryApi;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $this->_layerResolver->create(LayerResolver::CATALOG_LAYER_CATEGORY);
        $this->_processFilter($searchCriteria);
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->_layerResolver->get()->getProductCollection();
        $this->_extensionAttributesJoinProcessor->process($collection);

        $this->_setToolbar($collection);
        $collection->addAttributeToSelect('*');

        $this->_addExtensionAttributes($collection);
        $searchResult = $this->_searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }

    /**
     * @inheritdoc
     */
    public function search($searchTerm, SearchCriteriaInterface $searchCriteria)
    {
        $this->getRequest()->setParams(['q' => $searchTerm]);
        $query = $this->_queryFactory->get();
        $query->setStoreId($this->_storeManager->getStore()->getId());

        $this->_layerResolver->create(LayerResolver::CATALOG_LAYER_SEARCH);
        $this->_processFilter($searchCriteria);
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->_layerResolver->get()->getProductCollection();
        $this->_extensionAttributesJoinProcessor->process($collection);

        $this->_setToolbar($collection);
        $collection->addAttributeToSelect('*');
        $this->_addExtensionAttributes($collection);
        $searchResult = $this->_searchResultsFactory->create();
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }

    /**
     * @inheritdoc
     */
    public function searchSuggest($searchTerm)
    {
        $this->getRequest()->setParams(['q' => $searchTerm]);
        $autoCompleteData = $this->_autoComplete->getItems();
        $responseData = [];

        foreach ($autoCompleteData as $resultItem) {
            $responseData[] = $resultItem->toArray();
        }

        return $this->_dataObjectFactory->create()
            ->addData(['result' => $responseData])
            ->getData();
    }

    /**
     * @inheritdoc
     */
    public function searchPopular($storeId = null)
    {
        $store = $this->_storeManager->getStore($storeId);
        $queryCollection = $this->_queryFactory->create()
            ->getCollection()
            ->addFieldToFilter('updated_at', ['gteq' => (new \DateTime())->modify('-3 days')->format('Y-m-d H:i:s')])
            ->addFieldToFilter('num_results', ['gt' => 0])
            ->addFieldToFilter('display_in_terms', true)
            ->setStoreId($store)
            ->setCurPage(1)
            ->setOrder('popularity', 'desc')
            ->setPageSize(10);

        return $this->_dataObjectFactory->create()
            ->addData(['result' => $queryCollection->toArray()])
            ->getData();
    }

    /**
     * @inheritdoc
     */
    public function viewed($customerId)
    {
        /** @var \Magento\Reports\Block\Product\Viewed $viewedBlock */
        $viewedBlock = ObjectManager::getInstance()->get('Magento\Reports\Block\Product\Viewed');
        $collection = $viewedBlock
            ->setCustomerId($customerId)
            ->getItemsCollection();

        $this->_extensionAttributesJoinProcessor->process($collection);
        $collection->addAttributeToSelect('*');
        $this->_addExtensionAttributes($collection);

        $searchResult = $this->_searchResultsFactory->create();
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }

    /**
     * @inheritdoc
     */
    public function saveReview($productId, ReviewInterface $review, $customerId = null)
    {
        $product = $this->_productRepository->getById($productId);
        /** @var Review $reviewModel */
        $reviewModel = $this->_reviewFactory->create()
            ->setTitle($review->getTitle())
            ->setNickname($review->getNickname())
            ->setDetail($review->getDetail());

        $customerId = ($customerId) ?: $this->_customerSession->getCustomerId();
        $validate = $reviewModel->validate();
        if (!$validate) {
            throw new LocalizedException(__(implode(' ', $validate)));
        }

        $reviewModel->setEntityId($reviewModel->getEntityIdByCode(Review::ENTITY_PRODUCT_CODE))
            ->setEntityPkValue($product->getId())
            ->setStatusId(Review::STATUS_PENDING)
            ->setCustomerId($customerId)
            ->setStoreId($this->_storeManager->getStore()->getId())
            ->setStores([$this->_storeManager->getStore()->getId()])
            ->save();

        foreach ($this->form->getRatings() as $_rating) {
            $options = $_rating->getOptions();
            foreach ($options as $_option) {
                $rating = $review->getRating();
                if ($rating == $_option->getData('value')) {
                    $this->_ratingFactory->create()
                        ->setRatingId($_option->getData('rating_id'))
                        ->setReviewId($reviewModel->getId())
                        ->setCustomerId($customerId)
                        ->addOptionVote($_option->getData('option_id'), $product->getId());
                }
            }
        }
        $reviewModel->aggregate();
        return true;
    }

    /**
     * Set toolbar
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return $this
     */
    private function _setToolbar($collection)
    {
        $currentOrder = $this->getRequest()->getParam('product_list_order');
        $attribute = 'relevance';
        $dir = 'desc';

        if ($currentOrder) {
            $currentOrder = explode('-', $currentOrder);
            $attribute = $currentOrder[0];
            $dir = $currentOrder[1];
        }

        $collection->setOrder($attribute, $dir);
        $currentPage = $this->getRequest()->getParam(Toolbar::PAGE_PARM_NAME);

        if ($currentPage) {
            $limit = $this->getRequest()->getParam('limit');
            if (!$limit) {
                $limit = $this->_scopeConfig->getValue('catalog/frontend/grid_per_page', ScopeInterface::SCOPE_STORE);
            }

            $collection->setCurPage($currentPage);
            $collection->setPageSize($limit);
        }

        return $this;
    }

    /**
     * Process filter
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return $this
     */
    private function _processFilter(SearchCriteriaInterface $searchCriteria)
    {
        $request = $this->getRequest();
        $params = [];

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $params[$filter->getField()] = $filter->getValue();
            }
        }

        foreach ($this->_filterList->getFilters($this->_layerResolver->get()) as $filter) {
            $filter->apply($request->setParams($params));
        }

        return $this;
    }

    /**
     * Add extension attributes to loaded items.
     *
     * @param Collection $collection
     * @return Collection
     */
    private function _addExtensionAttributes(Collection $collection)
    {
        foreach ($collection->getItems() as $item) {
            $this->_readExtensions->execute($item);
        }

        return $collection;
    }

    /**
     * Get request
     *
     * @return \Magento\Framework\App\RequestInterface
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Get product links
     *
     * @param int $productId
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getLinks(int $productId) {
        return $this->getProductByCondition($productId, "related");
    }

    /**
     * Get product upsell
     *
     * @param int $productId
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getUpsell(int $productId) {
        return $this->getProductByCondition($productId, "upsell");
    }

    /**
     * Get link product by condition
     *
     * @param int $productId
     *
     * @param string $condition
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getProductByCondition( int $productId, string $condition ) {
        $product = $this->_productRepositoryApi->getById($productId);

        if (!empty($product)) {
            $linkProducts = $this->_productLinkRepositoryApi->getList($product);
            $resultProducts = [];

            if (!empty($linkProducts)) {
                foreach ($linkProducts as $linkProduct) {
                    if ($linkProduct->getLinkType() == $condition ) {
                        array_push( $resultProducts, $this->_productRepositoryApi
                            ->get( $linkProduct->getLinkedProductSku() ) );
                    }
                }
            }

            return $resultProducts;
        }
        return [];
    }
}
