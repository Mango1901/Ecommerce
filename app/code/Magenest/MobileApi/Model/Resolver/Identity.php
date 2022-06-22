<?php

namespace Magenest\MobileApi\Model\Resolver;

use Magento\Cms\Api\Data\BlockInterface;
use Magento\Framework\GraphQl\Query\Resolver\IdentityInterface;

/**
 * Identity for resolved CMS block
 */
class Identity implements IdentityInterface
{
    /** @var string */
    private $cacheTag = \Magento\Cms\Model\Block::CACHE_TAG;

    /**
     * Get block identities from resolved data
     *
     * @param array $resolvedData
     * @return string[]
     */
    public function getIdentities(array $resolvedData): array
    {
        $ids = [];

        if (!empty($resolvedData[BlockInterface::BLOCK_ID])) {
            $ids[] = sprintf('%s_%s', $this->cacheTag, $resolvedData[BlockInterface::BLOCK_ID]);
        }

        if (!empty($resolvedData[BlockInterface::IDENTIFIER])) {
            $ids[] = sprintf('%s_%s', $this->cacheTag, $resolvedData[BlockInterface::IDENTIFIER]);
        }

        if (!empty($ids)) {
            array_unshift($ids, $this->cacheTag);
        }

        return $ids;
    }
}
