<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Api;

use Magento\Customer\Api\Data\CustomerInterface;
use Magenest\MobileApi\Api\Data\Customer\VatInvoiceInterface;

interface AccountManagementInterface
{
    /**
     * Update Password
     *
     * @param int $customerId
     * @param string $email
     * @param string $currentPassword
     * @param string $newPassword
     *
     * @return bool
     */
    public function updatePassword($customerId, $email, $currentPassword, $newPassword);

    /**
     * Update Email
     *
     * @param int $customerId
     * @param string $newEmail
     * @param string $currentPassword
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function updateEmail($customerId, $newEmail, $currentPassword);

    /**
     * Update Profile
     *
     * @param int $customerId
     * @param \Magento\Customer\Api\Data\CustomerInterface $customerData
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function updateProfile($customerId, CustomerInterface $customerData);

    /**
     * Customer sign up
     *
     * @param Data\CustomerInterface $customer
     * @param string $password
     * @param string $guestQuoteId
     *
     * @return Data\CustomerInterface
     */
    public function signUp(Data\CustomerInterface $customer, $password = null, $guestQuoteId = null);

    /**
     * Customer sign in
     *
     * @param string $username
     * @param string|null $password
     * @param string|null $guestQuoteId
     *
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface
     */
    public function signIn($username, $password = null, $guestQuoteId = null);

    /**
     * Customer reset password
     *
     * @param string $telephone
     *
     * @return \Magenest\MobileApi\Api\ResetPasswordInterface
     */
    public function resetPassword($telephone);

    /**
     * Update Email
     *
     * @param int $customerId
     *
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface
     */
    public function reviews($customerId);

    /**
     * Facebook sign in
     *
     * @param string $accessToken
     * @param string $guestQuoteId
     *
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface
     */
    public function facebookSignIn($accessToken, $guestQuoteId = null);

    /**
     * Google sign in
     *
     * @param string $accessToken
     * @param string $guestQuoteId
     *
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface
     */
    public function googleSignIn($accessToken, $guestQuoteId = null);

    /**
     * Apple sign in and connect to existed account
     *
     * @param string $accessToken
     * @param string $state
     * @param string $guestQuoteId
     *
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface
     */
    public function appleSignIn($accessToken, $state = '{}', $guestQuoteId = null);

    /**
     * Get vat invoice
     *
     * @param int $customerId
     *
     * @return \Magenest\MobileApi\Api\Data\Customer\VatInvoiceInterface|null
     */
    public function vatInvoice($customerId);

    /**
     * Save vat invoice
     *
     * @param int $customerId
     * @param \Magenest\MobileApi\Api\Data\Customer\VatInvoiceInterface $vatInvoice
     *
     * @return \Magenest\MobileApi\Api\Data\Customer\VatInvoiceInterface
     */
    public function saveVatInvoice($customerId, VatInvoiceInterface $vatInvoice);

    /**
     * Save newsletter
     *
     * @param int $customerId
     * @param bool $isSubscribed
     *
     * @return int|bool
     */
    public function saveNewsletter($customerId, $isSubscribed);

    /**
     * Create or update a customer.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param string $passwordHash
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\InputException If bad input is provided
     * @throws \Magento\Framework\Exception\State\InputMismatchException If the provided email is already used
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveCustomerInformation(\Magento\Customer\Api\Data\CustomerInterface $customer, $password = null);
}
