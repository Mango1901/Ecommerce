<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Kangaroo extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Kangaroo
 */

namespace Magenest\Core\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ProductCollectionIsNew implements ObserverInterface
{
    private $_isProcessed = false;

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($this->_isProcessed) {
            return;
        }
        $collection = $observer->getEvent()->getCollection();
//        $collection->addAttributeToSelect('is_new', 'left');

        $this->_isProcessed = true;
    }
}
