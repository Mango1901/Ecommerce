<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Model\Affiliate;

use Magenest\MobileApi\Api\Data\Affiliate\SettingsInterface;
use Magento\Framework\DataObject;

/**
 * Class Settings
 * @package Magenest\MobileApi\Model\Affiliate
 */
class Settings extends DataObject implements SettingsInterface
{
    /** @const */
    const KEY_REFERRING_WEBSITE = 'referring_website';
    const KEY_PAYPAL_EMAIL = 'paypal_email';
    const KEY_RECEIVE_NOTIFICATIONS = 'receive_notifications';
    const KEY_ACCEPTED_TERMS_CONDITIONS = 'accepted_terms_conditions';
    const KEY_TELEPHONE = 'telephone';
    const KEY_IDENTIFY_NUMBER = 'identify_number';
    const KEY_BANK_ACCOUNT = 'bank_account';
    const KEY_BANK_NAME = 'bank_name';
    const KEY_IDENTIFY_CART_FIRST = 'identify_card_first';
    const KEY_IDENTIFY_CART_SECOND = 'identify_card_second';

    /**
     * @inheritdoc
     */
    public function getReferringWebsite()
    {
        return $this->getData(self::KEY_REFERRING_WEBSITE);
    }

    /**
     * @inheritdoc
     */
    public function setReferringWebsite($referringWebsite)
    {
        $this->setData(self::KEY_REFERRING_WEBSITE, $referringWebsite);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPaypalEmail()
    {
        return $this->getData(self::KEY_PAYPAL_EMAIL);
    }

    /**
     * @inheritdoc
     */
    public function setPaypalEmail($paypalEmail)
    {
        $this->setData(self::KEY_PAYPAL_EMAIL, $paypalEmail);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getReceiveNotifications()
    {
        return $this->getData(self::KEY_RECEIVE_NOTIFICATIONS);
    }

    /**
     * @inheritdoc
     */
    public function setReceiveNotifications($receiveNotifications)
    {
        $this->setData(self::KEY_RECEIVE_NOTIFICATIONS, $receiveNotifications);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAcceptedTermsConditions()
    {
        return $this->getData(self::KEY_ACCEPTED_TERMS_CONDITIONS);
    }

    /**
     * @inheritdoc
     */
    public function setAcceptedTermsConditions($acceptedTermsConditions)
    {
        $this->setData(self::KEY_ACCEPTED_TERMS_CONDITIONS, $acceptedTermsConditions);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTelephone()
    {
        return $this->getData(self::KEY_TELEPHONE);
    }

    /**
     * @inheritdoc
     */
    public function setTelephone($telephone)
    {
        $this->setData(self::KEY_TELEPHONE, $telephone);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIdentifyNumber()
    {
        return $this->getData(self::KEY_IDENTIFY_NUMBER);
    }

    /**
     * @inheritdoc
     */
    public function setIdentifyNumber($identifyNumber)
    {
        $this->setData(self::KEY_IDENTIFY_NUMBER, $identifyNumber);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBankAccount()
    {
        return $this->getData(self::KEY_BANK_ACCOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setBankAccount($bankAccount)
    {
        $this->setData(self::KEY_BANK_ACCOUNT, $bankAccount);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBankName()
    {
        return $this->getData(self::KEY_BANK_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setBankName($bankName)
    {
        $this->setData(self::KEY_BANK_NAME, $bankName);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIdentifyCardFirst()
    {
        return $this->getData(self::KEY_IDENTIFY_CART_FIRST);
    }

    /**
     * @inheritdoc
     */
    public function setIdentifyCardFirst($identifyCardFirst)
    {
        $this->setData(self::KEY_IDENTIFY_CART_FIRST, $identifyCardFirst);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIdentifyCardSecond()
    {
        return $this->getData(self::KEY_IDENTIFY_CART_SECOND);
    }

    /**
     * @inheritdoc
     */
    public function setIdentifyCardSecond($identifyCardSecond)
    {
        $this->setData(self::KEY_IDENTIFY_CART_SECOND, $identifyCardSecond);

        return $this;
    }
}