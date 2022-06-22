<?php

namespace Magenest\MobileApi\Model\Resolver\HotNewsHomePage\Query;

use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\ContextInterface;

interface PostsQueryInterface
{
    /**
     * Get posts search result
     *
     * @param array $args
     * @param ResolveInfo $info
     * @param ContextInterface $context
     * @return array
     */
    public function getResult(array $args, ResolveInfo $info, ContextInterface $context);
}
