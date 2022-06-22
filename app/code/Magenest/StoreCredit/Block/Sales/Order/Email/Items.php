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

namespace Magenest\StoreCredit\Block\Sales\Order\Email;

use Magento\Framework\App\ObjectManager;
use Magento\Sales\Block\Order\Email\Items\DefaultItems;
use Magenest\StoreCredit\Helper\Product;

/**
 * Class Items
 * @package Magenest\StoreCredit\Block\Sales\Order\Email
 */
class Items extends DefaultItems
{
    /**
     * Return store credit custom options
     *
     * @return array
     */
    public function getItemOptions()
    {
        /** @var Product $helper */
        $helper = ObjectManager::getInstance()->get(Product::class);
        $item = $this->getItem()->getOrderItem();

        return $helper->getOptionList($item, parent::getItemOptions());
    }
}
