<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ProductLabel extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_ProductLabel
 */

namespace Magenest\ProductLabel\Model\Indexer;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class LabelIndexer
 * @package Magenest\ProductLabel\Model\Indexer
 */
class LabelIndexer implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface
{

    /**
     * @var IndexBuilder
     */
    protected $_indexBuilderResource;

    /**
     * @var \Magenest\ProductLabel\Model\ResourceModel\LabelIndex
     */
    protected $_labelIndex;

    /**
     * @var \Magento\Framework\Indexer\CacheContext
     */
    protected $_cacheContext;

    /**
     * @var IndexBuilder
     */
    protected $_indexBuilder;

    /**
     * @var \Magenest\ProductLabel\Model\ResourceModel\LabelIndex
     */
    protected $_labelIndexResource;

    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * LabelIndexer constructor.
     * @param IndexBuilder $indexBuilder
     * @param \Magenest\ProductLabel\Model\ResourceModel\LabelIndex $labelIndex
     * @param \Magento\Framework\Indexer\CacheContext $cacheContext
     * @param \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
     */
    public function __construct(
        IndexBuilder $indexBuilder,
        \Magenest\ProductLabel\Model\ResourceModel\LabelIndex $labelIndex,
        \Magento\Framework\Indexer\CacheContext $cacheContext,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
    )
    {
        $this->_indexBuilder = $indexBuilder;
        $this->_labelIndexResource = $labelIndex;
        $this->_cacheContext = $cacheContext;
        $this->indexerRegistry = $indexerRegistry;
    }

    /**
     * @param int[] $ids
     * @throws LocalizedException
     */
    public function execute($ids)
    {
        try {
            $idsOld = $this->_labelIndexResource->getProductIdsByLabelId($ids);
            $this->_indexBuilder->reindexByLabelId($ids);
            $idsNew = $this->_labelIndexResource->getProductIdsByLabelId($ids);
            $arrayProductIds = array_merge($idsOld, $idsNew);
            $this->flushCacheByProductIds(array_unique($arrayProductIds));
        } catch (LocalizedException $e) {
            throw new LocalizedException(
                __("Magenest Product Label indexing failed. See details in exception log.")
            );
        }
    }

    /**
     * @throws LocalizedException
     */
    public function executeFull()
    {
        $idsOld = $this->_labelIndexResource->getAllProductIds();
        $this->_indexBuilder->reindexFull();
        $idsNew = $this->_labelIndexResource->getAllProductIds();
        $arrayProductIds = array_merge($idsOld, $idsNew);
        $this->flushCacheByProductIds(array_unique($arrayProductIds));

    }

    /**
     * @param array $ids
     * @throws LocalizedException
     */
    public function executeList(array $ids)
    {
        $this->execute($ids);
    }

    /**
     * @param $ids
     * @throws LocalizedException
     */
    public function executeRow($ids)
    {
        $indexer = $this->indexerRegistry->get('product_label');
        if (!$indexer->isScheduled()) {
            $this->execute([$ids]);
        }
    }

    /**
     * @param array $productIds
     */
    public function flushCacheByProductIds(array $productIds)
    {
        if ($productIds) {
            $this->_cacheContext->registerEntities(\Magento\Catalog\Model\Product::CACHE_TAG, $productIds);
        }
    }
}
