<?php

namespace Magenest\Customer\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;

/**
 * Class ConfigHelper
 */
class ConfigHelper extends AbstractHelper
{
    const XML_PATH_LOGIN_CUSTOMER_WITH_TELEPHONE = 'customer/startup/login_telephone';
    const XML_PATH_LOGOUT_REDIRECT_HOMEPAGE = 'customer/logout_options/redirect_homepage_when_logout';
    const XML_PATH_FULL_NAME_INSTEAD = 'customer/address/fullname_show';

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Context $context
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isEnabledLoginWithTelephone($storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = $this->getStoreId();
        }

        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_LOGIN_CUSTOMER_WITH_TELEPHONE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return int|null
     */
    public function getStoreId()
    {
        try {
            $storeId = $this->_storeManager->getStore()->getId();
        } catch (NoSuchEntityException $e) {
            $storeId = null;
        }

        return $storeId;
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isEnabledRedirectHomePageWhenLogout($storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = $this->getStoreId();
        }

        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_LOGOUT_REDIRECT_HOMEPAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isEnabledFullNameInstead($storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = $this->getStoreId();
        }

        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_FULL_NAME_INSTEAD,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param $fullName
     * @return array
     */
    public function splitFullNameToFirstLastName($fullName)
    {
        $fullNameArray = explode(' ', $fullName, 2);
        // Last name la Ho
        $result['lastname'] = $fullNameArray[0];
        // First name la Ten
        $result['firstname'] = count($fullNameArray) === 2 ? $fullNameArray[1] : 'User';

        return $result;
    }
}
