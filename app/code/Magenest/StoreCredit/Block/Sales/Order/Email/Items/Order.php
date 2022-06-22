<?php
/**
 * Magenest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magenest.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magenest.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magenest
 * @package     Magenest_StoreCredit
 * @copyright   Copyright (c) Magenest (https://www.magenest.com/)
 * @license     https://www.magenest.com/LICENSE.txt
 */

namespace Magenest\StoreCredit\Block\Sales\Order\Email\Items;

use Magento\Framework\App\ObjectManager;
use Magento\Sales\Block\Order\Email\Items\Order\DefaultOrder;
use Magenest\StoreCredit\Helper\Product;

/**
 * Class Order
 * @package Magenest\StoreCredit\Block\Order\Email\Items
 */
class Order extends DefaultOrder
{
    /**
     * @return array
     */
    public function getItemOptions()
    {
        /** @var Product $helper */
        $helper = ObjectManager::getInstance()->get(Product::class);
        $item = $this->getItem();

        return $helper->getOptionList($item, parent::getItemOptions());
    }
}
