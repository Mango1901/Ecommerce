<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MobileApi\Model\Filter;

use Magenest\MobileApi\Api\Filter\FilterManagementInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Search\Model\QueryFactory;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Catalog\Model\Layer\FilterList;
use Magento\Catalog\Model\Layer;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\DataObjectFactory;

/**
 * Class FilterManagement
 * @package Magenest\MobileApi\Model\Filter
 */
class FilterManagement implements FilterManagementInterface
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var QueryFactory
     */
    protected $queryFactory;

    /**
     * @var LayerResolver
     */
    protected $_layerResolver;

    /**
     * @var FilterList
     */
    protected $_filterList;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $_categoryRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var DataObjectFactory
     */
    protected $_dataObjectFactory;

    /**
     * Constructor.
     *
     * @param StoreManagerInterface $storeManagerInterface
     * @param CategoryRepositoryInterface $categoryRepository
     * @param FilterList $filterList
     * @param RequestInterface $request
     * @param QueryFactory $queryFactory
     * @param LayerResolver $layerResolver
     * @param DataObjectFactory $dataObjectFactory
     * @param array $data
     */
    public function __construct(
        StoreManagerInterface $storeManagerInterface,
        CategoryRepositoryInterface $categoryRepository,
        FilterList $filterList,
        RequestInterface $request,
        QueryFactory $queryFactory,
        LayerResolver $layerResolver,
        DataObjectFactory $dataObjectFactory,
        array $data = []
    )
    {
        $this->_request = $request;
        $this->_queryFactory = $queryFactory;
        $this->_layerResolver = $layerResolver;
        $this->_filterList = $filterList;
        $this->_categoryRepository = $categoryRepository;
        $this->_storeManager = $storeManagerInterface;
        $this->_dataObjectFactory = $dataObjectFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchFilters($searchTerm)
    {
        $this->getRequest()->setParams(['q' => $searchTerm]);
        $query = $this->_queryFactory->get();
        $query->setStoreId($this->_storeManager->getStore()->getId());
        $this->_layerResolver->create(LayerResolver::CATALOG_LAYER_SEARCH);

        return $this->getFilters();
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryFilters($categoryId)
    {
        $category = $this->_categoryRepository->get($categoryId, $this->_storeManager->getStore()->getId());
        $this->_layerResolver->create(LayerResolver::CATALOG_LAYER_CATEGORY);
        $this->_layerResolver->get()->setCurrentCategory($category);

        return $this->getFilters();
    }

    /**
     * Get filter
     *
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface
     */
    public function getFilters()
    {
        $result = [];

        foreach ($this->_filterList->getFilters($this->_layerResolver->get()) as $filter) {
            try {
                if ($filter->getItemsCount()) {
                    $filterName = $filter->getName();
                    $itemsData = [];

                    foreach ($filter->getItems() as $item) {
                        $label = $item->getLabel();
                        if (is_object($item->getLabel())) {
                            $label = $item->getLabel()->getText();
                            $arguments = $item->getLabel()->getArguments();
                            foreach ($arguments as $key => $argument) {
                                $label = str_replace('%' . ($key + 1), strip_tags($argument), $label);
                            }
                        }

                        $itemsData[] = [
                            'label' => $label,
                            'value' => $item->getValue(),
                            'count' => $item->getCount()
                        ];
                    }

                    $result[] = [
                        'label' => $filterName,
                        'value' => $itemsData,
                        'name' => $filter->getRequestVar()
                    ];
                }
            } catch (\Exception $e) {}
        }

        return $this->_dataObjectFactory->create()
            ->addData(['result' => $result])
            ->getData();
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
}
