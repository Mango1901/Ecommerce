<?php

namespace Magenest\MobileApi\Model\Resolver\Categories;

use Magento\Catalog\Model\Category;
use Magento\Framework\GraphQl\Query\Resolver\IdentityInterface;

/**
 * Identity for multiple resolved categories
 */
class CategoriesSameLevelIdentity implements IdentityInterface
{
    /** @var string */
    private $cacheTag = Category::CACHE_TAG;

    /**
     * Get category IDs from resolved data
     *
     * @param array $resolvedData
     * @return string[]
     */
    public function getIdentities(array $resolvedData): array
    {
        $ids = [];
        $resolvedCategories = $resolvedData['items'];
        if (!empty($resolvedCategories)) {
            foreach ($resolvedCategories as $category) {
                $ids[] = sprintf('%s_%s', $this->cacheTag, $category['id']);
            }
            if (!empty($ids)) {
                array_unshift($ids, $this->cacheTag);
            }
        }
        return $ids;
    }
}
