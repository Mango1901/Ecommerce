<?php


namespace Magenest\Affiliate\Api;

/**
 * Interface GuestCouponManagementInterface
 * @package Magenest\Affiliate\Api
 */
interface GuestCouponManagementInterface
{
    /**
     * Add a coupon by code to a specified cart.
     *
     * @param string $cartId The cart ID.
     * @param string $affiliateCouponCode The coupon code data.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The specified coupon could not be added.
     */
    public function set($cartId, $affiliateCouponCode);

    /**
     * Delete a coupon from a specified cart.
     *
     * @param string $cartId The cart ID.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotDeleteException The specified coupon could not be deleted.
     */
    public function remove($cartId);
}
