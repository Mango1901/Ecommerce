<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Api;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magenest\MobileApi\Api\Data\Affiliate\WidgetInterface;
use Magenest\MobileApi\Api\Data\Affiliate\SettingsInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Interface AffiliateManagementInterface
 * @package Magenest\MobileApi\Api
 */
interface AffiliateManagementInterface
{
    /**
     * Get customer programs
     *
     * @param int $customerId
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface
     * @throws LocalizedException
     */
    public function programs($customerId);

    /**
     * Get customer account
     *
     * @param int $customerId
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface
     * @throws LocalizedException
     */
    public function account($customerId);

    /**
     * Get customer promo
     *
     * @param int $customerId
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface
     * @throws LocalizedException
     */
    public function promo($customerId);

    /**
     * Save widget
     *
     * @param int $customerId
     * @param WidgetInterface $widget
     * @return bool
     * @throws LocalizedException
     * @throws CouldNotSaveException
     */
    public function saveWidget($customerId, WidgetInterface $widget);

    /**
     * Get traffic
     *
     * @param int $customerId
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface
     * @throws LocalizedException
     */
    public function traffic($customerId);

    /**
     * Get withdrawals
     *
     * @param int $customerId
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface
     * @throws LocalizedException
     */
    public function withdrawals($customerId);

    /**
     * Request withdrawals
     *
     * @param int $customerId
     * @param float $amount
     * @return bool|\Magenest\MobileApi\Api\Data\DataObjectInterface
     * @throws LocalizedException
     */
    public function requestWithdrawals($customerId, $amount);

    /**
     * Repeat withdrawals
     *
     * @param int $customerId
     * @param int $withdrawalId
     * @return bool|\Magenest\MobileApi\Api\Data\DataObjectInterface
     * @throws LocalizedException
     */
    public function repeatWithdrawals($customerId, $withdrawalId);

    /**
     * Cancel withdrawals
     *
     * @param int $customerId
     * @param int $withdrawalId
     * @return bool|\Magenest\MobileApi\Api\Data\DataObjectInterface
     * @throws LocalizedException
     */
    public function cancelWithdrawals($customerId, $withdrawalId);

    /**
     * Save settings
     *
     * @param int $customerId
     * @param SettingsInterface $settings
     * @return \Magenest\MobileApi\Api\Data\DataObjectInterface
     * @throws LocalizedException
     */
    public function saveSettings($customerId, SettingsInterface $settings);
}