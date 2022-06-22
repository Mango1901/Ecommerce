<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\CustomCatalogSearch\Plugins\Model\Indexer\Fulltext\Action;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\CatalogSearch\Model\Indexer\Fulltext;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;

/**
 * Class DataProvider
 * @package Magenest\CustomizeCatalogSearch\Plugins\Model\Indexer\Fulltext\Action
 */
class DataProvider
{
    /**
     * After get product child ids
     *
     * @param $subject
     * @param $result
     * @return null
     */
    public function afterGetProductChildIds($subject, $result)
    {
        return null;
    }
}
