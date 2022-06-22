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

namespace Magenest\StoreCredit\Model\Quote\Item;

use Magento\Framework\DataObject\Factory;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote\Item\CartItemProcessorInterface;

/**
 * Class CartItemProcessor
 * @package Magenest\StoreCredit\Model\Quote\Item
 */
class CartItemProcessor implements CartItemProcessorInterface
{
    /**
     * @var Factory
     */
    protected $objectFactory;

    /**
     * CartItemProcessor constructor.
     *
     * @param Factory $objectFactory
     */
    public function __construct(
        Factory $objectFactory
    ) {
        $this->objectFactory = $objectFactory;
    }

    /**
     * @inheritdoc
     */
    public function convertToBuyRequest(CartItemInterface $cartItem)
    {
        if ($cartItem->getProductOption() &&
            $cartItem->getProductOption()->getExtensionAttributes() &&
            $cartItem->getProductOption()->getExtensionAttributes()->getCreditAmount()
        ) {
            return $this->objectFactory->create(
                [
                    'credit_amount' => $cartItem->getProductOption()
                        ->getExtensionAttributes()->getCreditAmount()
                ]
            );
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function processOptions(CartItemInterface $cartItem)
    {
        return $cartItem;
    }
}
