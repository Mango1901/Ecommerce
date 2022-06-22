<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MobileApi\Controller\Payment;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\UrlInterface;

/**
 * Class Paypal
 * @package Magenest\MobileApi\Controller\Payment
 */
class Paypal extends Action
{
    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * @var  PhpCookieManager
     */
    protected $_cookieMetadataManager;

    /**
     * @var  CookieMetadataFactory
     */
    protected $_cookieMetadataFactory;

    /**
     * @var AccountManagementInterface
     */
    protected $_customerAccountManagement;

    /**
     * @var UrlInterface
     */
    protected $_url;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param AccountManagementInterface $accountManagement
     * @param UrlInterface $urlInterface
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        AccountManagementInterface $accountManagement,
        UrlInterface $urlInterface,
        PhpCookieManager $phpCookieManager = null,
        CookieMetadataFactory $cookieMetadataFactory = null
    )
    {
        parent::__construct($context);
        $this->_customerSession = $customerSession;
        $this->_customerAccountManagement = $accountManagement;
        $this->_url = $urlInterface;
        $this->_cookieMetadataManager = $phpCookieManager ?? ObjectManager::getInstance()->get(PhpCookieManager::class);
        $this->_cookieMetadataFactory = $cookieMetadataFactory ?? ObjectManager::getInstance()->get(CookieMetadataFactory::class);;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try{
            if (!$this->_customerSession->isLoggedIn()) {
                $this->_logIn();
            }

            if ($this->_customerSession->getCustomer()->getEmail() != $this->getRequest()->getParam('username')) {
                $this->_logOut();
                $this->_logIn();
            }

            $url = $this->_url->getUrl('paypal/express/start', array('button' => 1));
        }catch (\Exception $e){
            $this->messageManager->addError(__($e->getMessage()));
            $url = $this->_url->getBaseUrl();
        }

        $this->getResponse()->setRedirect($url);
        return;
    }

    /**
     * Login
     */
    private function _logIn()
    {
        $login = $this->getRequest()->getParams();
        $customer = $this->_customerAccountManagement->authenticate($login['username'], $login['password']);
        $this->_customerSession->setCustomerDataAsLoggedIn($customer);
        $this->_customerSession->regenerateId();

        if ($this->_getCookieManager()->getCookie('mage-cache-sessid')) {
            $metadata = $this->_getCookieMetadataFactory()->createCookieMetadata();
            $metadata->setPath('/');
            $this->_getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
        }
    }


    /**
     * Logout
     */
    private function _logOut()
    {
        $lastCustomerId = $this->_customerSession->getId();
        $this->_customerSession->logout()->setBeforeAuthUrl($this->_redirect->getRefererUrl())
            ->setLastCustomerId($lastCustomerId);

        if ($this->_getCookieManager()->getCookie('mage-cache-sessid')) {
            $metadata = $this->_getCookieMetadataFactory()->createCookieMetadata();
            $metadata->setPath('/');
            $this->_getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
        }
    }

    /**
     * Retrieve cookie manager
     *
     * @return PhpCookieManager
     */
    private function _getCookieManager()
    {
        return $this->_cookieMetadataManager;
    }

    /**
     * Retrieve cookie metadata factory
     *
     * @return CookieMetadataFactory
     */
    private function _getCookieMetadataFactory()
    {
        return $this->_cookieMetadataFactory;
    }
}
