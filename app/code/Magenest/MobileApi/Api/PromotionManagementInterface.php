<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MobileApi\Api;

/**
 * Interface PromotionManagementInterface
 * @package Magenest\MobileApi\Api
 */
interface PromotionManagementInterface
{
    /**
     * Get free gift options
     *
     * @param int $cartId
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface
     */
    public function getFreeGiftOptions($cartId);

    /**
     * Get guest free gift options
     *
     * @param string $cartId
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface
     */
    public function getGuestFreeGiftOptions($cartId);

    /**
     * Add gifts
     *
     * @param int $cartId
     * @param \Magento\Quote\Api\Data\CartItemInterface[] $gifts
     * @return \Magento\Quote\Api\Data\CartInterface
     */
    public function addGifts($cartId, $gifts);

    /**
     * Add gifts to guest cart
     *
     * @param string $cartId
     * @param \Magento\Quote\Api\Data\CartItemInterface[] $gifts
     * @return \Magento\Quote\Api\Data\CartInterface
     */
    public function addGuestGifts($cartId, $gifts);
}
