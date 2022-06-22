<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_tn233 extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Kangaroo
 */

namespace Magenest\FormatPrice\Observer;

use Magento\Framework\Currency;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ChangeCurrencyPosition implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $currencyOptions = $observer->getEvent()->getCurrencyOptions();
        $currencyOptions->setData('position', Currency::RIGHT);
        $currencyOptions->setData('locale', 'vi_VN');
        return $this;
    }
}
