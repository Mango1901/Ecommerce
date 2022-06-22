<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model\Export\RowCustomizer;

use Firebear\ImportExport\Model\Export\Product\Bundle\RowCustomizer as BundleRowCustomizer;

/**
 * Class Composite
 *
 * @package Firebear\ImportExport\Model\Export\RowCustomizer
 */
class Composite extends \Magento\CatalogImportExport\Model\Export\RowCustomizer\Composite
{

    /**
     * @param array $dataRow
     * @param int $productId
     * @return array
     */
    public function addData($dataRow, $productId)
    {
        $moduleManager = $this->getModuleManager();

        foreach ($this->customizers as $key => $className) {
            if ($key == 'bundleProduct') {
                $className = BundleRowCustomizer::class;
            }
            if ($key == 'giftcard' && !$moduleManager->isEnabled('Magento_GiftCard')) {
                continue;
            }
            $dataRow = $this->objectManager->get($className)->addData($dataRow, $productId);
        }
        return $dataRow;
    }

    /**
     * Set headers columns
     *
     * @param array $columns
     * @return array
     */
    public function addHeaderColumns($columns)
    {
        $moduleManager = $this->getModuleManager();

        foreach ($this->customizers as $key => $className) {
            if ($key == 'giftcard' && !$moduleManager->isEnabled('Magento_GiftCard')) {
                continue;
            }
            $columns = $this->objectManager->get($className)->addHeaderColumns($columns);
        }
        return $columns;
    }

    /**
     * @param mixed $collection
     * @param int[] $productIds
     * @return mixed
     */
    public function prepareData($collection, $productIds)
    {
        $moduleManager = $this->getModuleManager();

        foreach ($this->customizers as $key => $className) {
            if ($key == 'bundleProduct') {
                $className = BundleRowCustomizer::class;
            }
            if ($key == 'giftcard' && !$moduleManager->isEnabled('Magento_GiftCard')) {
                continue;
            }
            $this->objectManager->get($className)->prepareData($collection, $productIds);
        }
    }

    /**
     * Get Module Manager object.
     *
     * @return mixed
     */
    protected function getModuleManager()
    {
        return $this->objectManager->get(\Magento\Framework\Module\Manager::class);
    }
}
