<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MobileApi\Api;

use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Interface CartManagementInterface
 * @pacakge Magenest\MobileApi\Api
 */
interface CartManagementInterface
{
    /**
     * Merge guest cart to logged in customer cart
     *
     * @param int $customerId
     * @param string $guestQuoteId
     * @return bool
     */
    public function mergeGuestCart($customerId, $guestQuoteId);

    /**
     * Set payment information and place order for a specified cart.
     *
     * @param int $cartId
     * @param PaymentInterface $paymentMethod
     * @param AddressInterface|null $billingAddress
     * @throws CouldNotSaveException
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function savePaymentInformationAndPlaceOrder($cartId, PaymentInterface $paymentMethod, AddressInterface $billingAddress = null);

    /**
     * Set payment information and place order for a specified cart.
     *
     * @param string $cartId
     * @param string $email
     * @param PaymentInterface $paymentMethod
     * @param AddressInterface|null $billingAddress
     * @throws CouldNotSaveException
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function savePaymentInformationAndPlaceGuestOrder($cartId, $email, PaymentInterface $paymentMethod, AddressInterface $billingAddress = null);

    /**
     * Get product from cart item
     *
     * @param string $cartId
     * @param int $itemId
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throw NoSuchEntityException
     */
    public function guestItemProduct($cartId, $itemId);

    /**
     * Get product from cart item
     *
     * @param int $cartId
     * @param int $itemId
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throw NoSuchEntityException
     */
    public function itemProduct($cartId, $itemId);

    /**
     * Get guest payment method list
     *
     * @param string $cartId
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface
     * @throw NoSuchEntityException
     */
    public function getGuestPaymentMethodList($cartId);

    /**
     * Get payment method list
     *
     * @param string $cartId
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface
     * @throw NoSuchEntityException
     */
    public function getPaymentMethodList($cartId);
}
