<?php

namespace Magenest\MobileApi\Model\Resolver;

use Magenest\MobileApi\Model\Resolver\Banner\Query\BannerQueryInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class BannerHomePage implements ResolverInterface
{
    /**
     * @var BannerQueryInterface
     */
    private $searchQuery;

    /**
     * @param BannerQueryInterface $searchQuery
     */
    public function __construct(
        BannerQueryInterface $searchQuery
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
        return $searchResult['searchResult'];
    }
}
