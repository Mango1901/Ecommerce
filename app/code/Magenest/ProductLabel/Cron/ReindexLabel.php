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

namespace Magenest\ProductLabel\Cron;

/**
 * Class ReindexLabel
 * @package Magenest\ProductLabel\Cron
 */
class ReindexLabel
{
    /**
     * @var \Magenest\ProductLabel\Model\Indexer\LabelIndexer
     */
    protected $_labelIndexer;

    /**
     * @var \Magento\Framework\Indexer\CacheContextFactory
     */
    protected $_cacheContextFactory;

    /**
     * @var \Magenest\ProductLabel\Model\ResourceModel\LabelIndex
     */
    protected $_labelIndex;

    /**
     * ReindexLabel constructor.
     *
     * @param \Magenest\ProductLabel\Model\Indexer\LabelIndexer $labelProductIndexer
     */
    public function __construct(
        \Magenest\ProductLabel\Model\Indexer\LabelIndexer $labelProductIndexer
    ) {
        $this->_labelIndexer = $labelProductIndexer;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $this->_labelIndexer->executeFull();
    }


}
