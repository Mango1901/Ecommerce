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

namespace Magenest\OrderManagement\Plugin\Magento\Sales\Block\Adminhtml\Order\View;

class Items
{
    public function afterGetColumns(\Magento\Sales\Block\Adminhtml\Order\View\Items $items, $result)
    {
        if (is_array($result)) {
            unset($result['tax-amount']);
            unset($result['tax-percent']);
        }

        return $result;
    }
}
