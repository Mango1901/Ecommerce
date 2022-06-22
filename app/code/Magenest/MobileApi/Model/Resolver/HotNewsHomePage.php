<?php

namespace Magenest\MobileApi\Model\Resolver;

use Magenest\MobileApi\Model\Resolver\HotNewsHomePage\Query\PostsQueryInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class HotNewsHomePage implements ResolverInterface
{
    /**
     * @var PostsQueryInterface
     */
    private $searchQuery;

    /**
     * @param PostsQueryInterface $searchQuery
     */
    public function __construct(
        PostsQueryInterface $searchQuery
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
        $searchResult = $this->searchQuery->getResult($args, $info, $context);
        return [
            'items' => $searchResult['postsSearchResult'],
            'block_id' => $searchResult['block_id'],
            'identifier' => $searchResult['identifier'],
        ];
    }
}
