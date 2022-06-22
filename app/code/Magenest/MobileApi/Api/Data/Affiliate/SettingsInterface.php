<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Api\Data\Affiliate;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface SettingsInterface
 * @package Magenest\MobileApi\Api\Data\Affiliate
 */
interface SettingsInterface extends ExtensibleDataInterface
{
    /**
     * Get referring website
     *
     * @return string
     * @since 102.0.0
     */
    public function getReferringWebsite();

    /**
     * Set referring website
     *
     * @param string $referringWebsite
     * @return $this
     */
    public function setReferringWebsite($referringWebsite);

    /**
     * Get paypal email
     *
     * @return string
     * @since 102.0.0
     */
    public function getPaypalEmail();

    /**
     * Set pay
     *
     * @param string $paypalEmail
     * @return $this
     */
    public function setPaypalEmail($paypalEmail);

    /**
     * Get receive notifications
     *
     * @return bool
     * @since 102.0.0
     */
    public function getReceiveNotifications();

    /**
     * Set receive notifications
     *
     * @param bool $receiveNotifications
     * @return $this
     */
    public function setReceiveNotifications($receiveNotifications);

    /**
     * Get accepted termsConditions
     *
     * @return bool
     * @since 102.0.0
     */
    public function getAcceptedTermsConditions();

    /**
     * Set accepted termsConditions
     *
     * @param bool $acceptedTermsConditions
     * @return $this
     */
    public function setAcceptedTermsConditions($acceptedTermsConditions);

    /**
     * Get telephone
     *
     * @return string
     * @since 102.0.0
     */
    public function getTelephone();

    /**
     * Set telephone
     *
     * @param string $telephone
     * @return $this
     */
    public function setTelephone($telephone);

    /**
     * Get identify number
     *
     * @return string
     * @since 102.0.0
     */
    public function getIdentifyNumber();

    /**
     * Set identify number
     *
     * @param string $identifyNumber
     * @return $this
     */
    public function setIdentifyNumber($identifyNumber);

    /**
     * Get bank account
     *
     * @return string
     * @since 102.0.0
     */
    public function getBankAccount();

    /**
     * Set bank account
     *
     * @param string $bankAccount
     * @return $this
     */
    public function setBankAccount($bankAccount);

    /**
     * Get bank name
     *
     * @return string
     * @since 102.0.0
     */
    public function getBankName();

    /**
     * Set bank name
     *
     * @param string $bankName
     * @return $this
     */
    public function setBankName($bankName);

    /**
     * Get identify card first
     *
     * @return string
     * @since 102.0.0
     */
    public function getIdentifyCardFirst();

    /**
     * Set identify card first
     *
     * @param string $identifyCardFirst
     * @return $this
     */
    public function setIdentifyCardFirst($identifyCardFirst);

    /**
     * Get identify card second
     *
     * @return string
     * @since 102.0.0
     */
    public function getIdentifyCardSecond();

    /**
     * Set identify card second
     *
     * @param string $identifyCardSecond
     * @return $this
     */
    public function setIdentifyCardSecond($identifyCardSecond);
}
