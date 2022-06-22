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

namespace Magenest\OrderManagement\Block\Adminhtml\Items\Column;

class Backorders extends \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn
{
    public function toHtml()
    {
        $item = $this->getItem();

        return $item->getQtyBackordered() > 0 ? (int)$item->getQtyBackordered() : __("No");
    }
}
