<?php

namespace Magenest\MobileApi\Model\Resolver;

use Magenest\MobileApi\Model\Resolver\Slider\Query\SliderQueryInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class SliderHomePage
 */
class SliderHomePage implements ResolverInterface
{
    /**
     * @var SliderQueryInterface
     */
    private $searchQuery;

    /**
     * @param SliderQueryInterface $searchQuery
     */
    public function __construct(
        SliderQueryInterface $searchQuery
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
            'items' => $searchResult['searchResult'],
            'total_count' => count($searchResult['searchResult']),
            'block_id' => $searchResult['block_id'],
            'identifier' => $searchResult['identifier'],
        ];
    }
}
