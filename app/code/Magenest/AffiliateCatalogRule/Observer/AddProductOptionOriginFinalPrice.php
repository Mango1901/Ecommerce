<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 04/11/2021
 * Time: 15:39
 */

namespace Magenest\AffiliateCatalogRule\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AddProductOptionOriginFinalPrice implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $configObj = $observer->getData('configObj');
        $configOptions = $configObj->getConfig();
        $revert = false;
        if (isset($configOptions['prices'])) {
            $configOptions = [$configOptions];
            $revert = true;
        }
        foreach ($configOptions as &$configOption) {
            if (!is_array($configOption)) {
                continue;
            }
            foreach ($configOption as &$option) {
                if (isset($option['prices'])) {
                    $option['prices']['originFinalPrice'] = $option['prices']['finalPrice'];
                }
            }
        }
        if ($revert) {
            $configOptions = $configOptions[0];
        }
        $configObj->setData('config', $configOptions);
    }
}
