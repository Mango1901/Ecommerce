<?php

namespace Magenest\MobileApi\Model\Resolver;

use Magenest\MobileApi\Model\Resolver\CmsBlockColumnHomePage\Query\ColumnQueryInterface;
use Magenest\MobileApi\Setup\Patch\Data\HuntSaleImmediatelyHomePage;
use Magenest\MobileApi\Setup\Patch\Data\MostWatchHomePage;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class BlockCmsColumnHomePage implements ResolverInterface
{
    const ALLOW_IDENTIFIER = [
        HuntSaleImmediatelyHomePage::HUNT_SALE_IMMEDIATELY_HOME_PAGE_MOBILE,
        MostWatchHomePage::MOST_WATCH_HOME_PAGE_MOBILE,
    ];

    /**
     * @var ColumnQueryInterface
     */
    private $searchQuery;

    /**
     * @param ColumnQueryInterface $searchQuery
     */
    public function __construct(
        ColumnQueryInterface $searchQuery
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
            'items' => $searchResult['columnsSearchResult'],
            'total_count' => count($searchResult['columnsSearchResult']),
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
    }
}
