<?php

namespace Magenest\MobileApi\Model\Resolver\Slider\Query;

use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\ContextInterface;

interface SliderQueryInterface
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
