<?php

namespace Magenest\FastErp\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class SetItemDimensionAttribute
 */
class SetItemDimensionAttribute implements ObserverInterface
{
    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        $quoteItem = $observer->getQuoteItem();
        /**
         * @var \Magento\Catalog\Model\Product $product
         */
        $product = $observer->getProduct();
        if ($widthAttribute = $product->getCustomAttribute('width')) {
            $quoteItem->setWidth($widthAttribute->getValue());
        }

        if ($heightAttribute = $product->getCustomAttribute('height')) {
            $quoteItem->setHeight($heightAttribute->getValue());
        }

        if ($lengthAttribute = $product->getCustomAttribute('length')) {
            $quoteItem->setLength($lengthAttribute->getValue());
        }
    }
}
