<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\MobileApi\Model\Resolver;

use Magenest\MobileApi\Model\Resolver\CmsBlockProductHomePage\Query\ProductQuery;
use Magenest\MobileApi\Setup\Patch\Data\BestSellerHomePage;
use Magenest\MobileApi\Setup\Patch\Data\SuperSaleHomePage;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class BlockCmsProductHomePage resolver, used for GraphQL request processing.
 */
class BlockCmsProductHomePage implements ResolverInterface
{
    const ALLOW_IDENTIFIER = [
        BestSellerHomePage::BEST_SELLER_HOME_PAGE_MOBILE,
        SuperSaleHomePage::SUPER_SALE_HOME_PAGE_MOBILE
    ];

    /**
     * @var ProductQuery
     */
    private $searchQuery;

    /**
     * @param ProductQuery $searchQuery
     */
    public function __construct(
        ProductQuery $searchQuery
    ) {
        $this->searchQuery = $searchQuery;
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $this->validateInput($args);
        $searchResult = $this->searchQuery->getResult($args, $info, $context);
        return [
            'total_count' => $searchResult['totalCount'],
            'items' => $searchResult['productsSearchResult'],
            'page_info' => [
                'page_size' => $searchResult['pageSize'],
                'current_page' => $searchResult['currentPage'],
                'total_pages' => $searchResult['totalPages']
            ],
            'category_id' => $searchResult['categoryId'],
            'block_id' => $searchResult['block_id'],
            'identifier' => $searchResult['identifier'],
        ];
    }

    /**
     * Validate input arguments
     *
     * @param array $args
     * @throws GraphQlAuthorizationException
     * @throws GraphQlInputException
     */
    private function validateInput(array $args)
    {
        if (!in_array(
            $args['identifier'],
            self::ALLOW_IDENTIFIER
        )) {
            throw new GraphQlInputException(__('Not allow query data for identifier.'));
        }

        if ($args['currentPage'] < 1) {
            throw new GraphQlInputException(__('currentPage value must be greater than 0.'));
        }
        if ($args['pageSize'] < 1) {
            throw new GraphQlInputException(__('pageSize value must be greater than 0.'));
        }
    }
}
