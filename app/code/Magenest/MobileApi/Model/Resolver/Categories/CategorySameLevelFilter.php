<?php

namespace Magenest\MobileApi\Model\Resolver\Categories;

use Magenest\CustomCatalog\Setup\Patch\Data\AddIncludeInCategoryMenu;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\UrlInterface;
use Magento\GraphQl\Model\Query\ContextInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Category filter allows filtering category results by attributes.
 */
class CategorySameLevelFilter
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param CategoryRepositoryInterface $categoryRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * Search for categories
     *
     * @param array $args
     * @param StoreInterface $store
     * @param array $attributeNames
     * @param ContextInterface $context
     * @return int[]
     * @throws GraphQlInputException
     */
    public function getResult(array $args, StoreInterface $store, array $attributeNames, ContextInterface $context)
    {
        try {
            $currentCategory = $this->categoryRepository->get($args['id'], $store->getId());

            $level = $currentCategory->getLevel();
            if ($level == 2) {
                $subcategories = $currentCategory->getChildrenCategories();
            } else {
                $subcategories = $currentCategory->getParentCategory()->getChildrenCategories();
            }
            $subcategories
                ->addAttributeToSelect('category_icon')
                ->addAttributeToSelect('image')
                ->addAttributeToFilter(AddIncludeInCategoryMenu::ATTRIBUTE_CODE, 1);

            $subcategories->setPageSize($args['pageSize'] ?? 5);
            $subcategories->setCurPage($args['currentPage'] ?? 1);

            $categoryDatas = [];
            foreach ($subcategories->getItems() as $category) {
                $categoryDatas[] =[
                    'id' => $category->getId(),
                    'image' => $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB) .
                        $category->getImageUrl('category_icon'),
                    'name' => $category->getName(),
                    'product_count' => $category->getProductCount(),
                    'level' => $category->getLevel(),
                    'model' => $category
                ];
            }
        } catch (\Exception $exception) {
            throw new GraphQlInputException(__($exception->getMessage()));
        }

        return [
            'items' => $categoryDatas,
            'total_count' => $subcategories->getSize(),
            'page_info' => [
                'total_pages' => $subcategories->getLastPageNumber(),
                'page_size' => $subcategories->getPageSize(),
                'current_page' => $subcategories->getCurPage(),
            ]
        ];
    }
}
