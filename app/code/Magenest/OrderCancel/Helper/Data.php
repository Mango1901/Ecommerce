<?php

namespace Magenest\OrderCancel\Helper;

use Magento\Store\Model\ScopeInterface;

/**
 * Helper Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const ENABLE_SEND_SMS_CANCEL_ORDER = 'order_cancel/cancelation_sms/enabled';
    const SMS_BRANCH_NAME = 'order_cancel/cancelation_sms/brand_name';
    const SMS_USERNAME = 'order_cancel/cancelation_sms/username';
    const SMS_PASSWORD = 'order_cancel/cancelation_sms/password';
    const SMS_MESSAGE = 'order_cancel/cancelation_sms/message';
    const SMS_PHONE = 'order_cancel/cancelation_sms/phone';

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isEnabledSendSmsCancelOrder($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::ENABLE_SEND_SMS_CANCEL_ORDER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getBranchName($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::SMS_BRANCH_NAME,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getUsername($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::SMS_USERNAME,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getPassword($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::SMS_PASSWORD,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getMessage($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::SMS_MESSAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getPhone($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::SMS_PHONE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
