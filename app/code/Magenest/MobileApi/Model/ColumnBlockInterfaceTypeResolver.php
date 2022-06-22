<?php

namespace Magenest\MobileApi\Model;

use Magento\Framework\GraphQl\Query\Resolver\TypeResolverInterface;

/**
 * {@inheritdoc}
 */
class ColumnBlockInterfaceTypeResolver implements TypeResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function resolveType(array $data) : string
    {
        if (isset($data['more_info'])) {
            return 'MostWatch';
        }
        return 'HuntSale';
    }
}
