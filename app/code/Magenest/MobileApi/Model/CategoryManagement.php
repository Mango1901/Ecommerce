<?php
namespace Magenest\MobileApi\Model;

use Magenest\MobileApi\Api\CategoryManagementInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\EntityManager\Operation\Read\ReadExtensions;
use Magento\Catalog\Api\CategoryRepositoryInterface;

class CategoryManagement implements CategoryManagementInterface
{
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
     * @param JoinProcessorInterface $joinProcessor
     * @param ProductSearchResultsInterfaceFactory $productSearchResultsFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ReadExtensions $readExtensions
     * @param StockItemRepositoryInterface $stockItemRepositoryInterface
     */
    function __construct(
        JoinProcessorInterface $joinProcessor,
        ProductSearchResultsInterfaceFactory $productSearchResultsFactory,
        CategoryRepositoryInterface $categoryRepository,
        ReadExtensions $readExtensions
    )
    {
        $this->categoryRepository = $categoryRepository;
        $this->_extensionAttributesJoinProcessor = $joinProcessor;
        $this->_searchResultsFactory = $productSearchResultsFactory;
        $this->_readExtensions = $readExtensions;
    }

    /**
     * Get products by category id
     *
     * @param int $categoryId
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAssignedProducts($categoryId)
    {
        $category = $this->categoryRepository->get($categoryId);

        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $category->getProductCollection();
        $collection->addFieldToSelect('*');
        $this->_extensionAttributesJoinProcessor->process($collection);

        $this->_addExtensionAttributes($collection);
        $searchResult = $this->_searchResultsFactory->create();
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
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
}
