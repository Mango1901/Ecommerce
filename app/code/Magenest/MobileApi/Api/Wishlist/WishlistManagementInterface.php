<?php
/**
 * Created by PhpStorm.
 * User: thuy
 * Date: 25/07/2019
 * Time: 12:52
 */

namespace Magenest\MobileApi\Api\Wishlist;


interface WishlistManagementInterface
{
    /**
     * Return Wishlist items.
     *
     * @param int $customerId
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface
     */
    public function getWishlistForCustomer($customerId);

    /**
     * Return Added wishlist item.
     *
     * @param int $customerId
     * @param int $productId
     * @param \Magenest\MobileApi\Api\Data\DataObjectInterface|null $buyRequest
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface
     *
     */
    public function addWishlistForCustomer($customerId, $productId, $buyRequest);

    /**
     * Return Added wishlist item.
     *
     * @param int $customerId
     * @param int $wishlistId
     * @return bool
     *
     */
    public function deleteWishlistForCustomer($customerId,$wishlistItemId);

    /**
     * Return Added wishlist info.
     *
     * @param int $customerId
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface
     *
     */
    public function getWishlistInfo($customerId);
}