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

namespace Magenest\ProductLabel\Model\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Indexer\IndexerRegistry;

/**
 * Class Save
 * @package Magenest\ProductLabel\Model\Observer
 */
class Save implements ObserverInterface
{
    const INDEXER_ID = 'magenest_label_product';
    /**
     * @var IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * @var \Magenest\ProductLabel\Model\Indexer\Product\ProductRuleIndexer
     */
    protected $labelIndexer;

    /**
     * @var \Magento\Catalog\Helper\Product|null
     */
    protected $_catalogProduct = null;

    /**
     * Save constructor.
     * @param \Magenest\ProductLabel\Model\Indexer\Product\ProductRuleIndexer $labelIndexer
     * @param \Magento\Catalog\Helper\Product $catalogProduct
     * @param IndexerRegistry $indexerRegistry
     */
    public function __construct(
        \Magenest\ProductLabel\Model\Indexer\Product\ProductRuleIndexer $labelIndexer,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
    ) {
        $this->labelIndexer = $labelIndexer;
        $this->_catalogProduct = $catalogProduct;
        $this->indexerRegistry = $indexerRegistry;
    }

    /**
     * @param EventObserver $observer
     * @return void
     * @throws \Exception
     */
    public function execute(EventObserver $observer)
    {
        $product = $observer->getData('product');
        if ($this->_catalogProduct->isDataForProductCategoryIndexerWasChanged($product)) {
            $productCategoryIndexer = $this->indexerRegistry->get(self::INDEXER_ID);
            if (!$productCategoryIndexer->isScheduled()) {
                $this->labelIndexer->doExecuteList(array($product->getEntityId()));
            }
        }
    }
}
