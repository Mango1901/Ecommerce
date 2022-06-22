<?php


namespace Magenest\Affiliate\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Magento\Store\Model\StoreManagerInterface;
use Magenest\Affiliate\Helper\Data;
use Magenest\Affiliate\Model\AccountFactory;
use Magenest\Affiliate\Model\Config\Source\Urlparam;

/**
 * Class PredispatchObserver
 * @package Magenest\Affiliate\Observer
 */
class PredispatchObserver implements ObserverInterface
{
    /**
     * @var AccountFactory
     */
    protected $_accountFactory;

    /**
     * @var Data
     */
    protected $_helper;

    /**
     * @var Urlparam
     */
    protected $urlParam;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * PredispatchObserver constructor.
     *
     * @param AccountFactory $accountFactory
     * @param Data $helper
     * @param Urlparam $urlParam
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        AccountFactory $accountFactory,
        Data $helper,
        Urlparam $urlParam,
        StoreManagerInterface $storeManager
    ) {
        $this->_accountFactory = $accountFactory;
        $this->_helper = $helper;
        $this->urlParam = $urlParam;
        $this->storeManager = $storeManager;
    }

    /**
     * @param Observer $observer
     *
     * @return mixed
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException *@throws
     *     \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $urlPrefix = $this->_helper->getUrlPrefix() ?: 'u';
        $key = $observer->getRequest()->getParam($urlPrefix);
        if (!$key || !$this->_helper->isEnabled()) {
            return $this;
        }

        $account = $this->_accountFactory->create();

        $urlParams = $this->urlParam->getOptionHash();
        foreach ($urlParams as $code => $label) {
            $account->load($key, $code);
            if ($account->getId()) {
                if ($account->isActive()) {
                    $isSetCookie = $this->checkIsSetCookie();

                    if ($isSetCookie) {
                        $this->setAffiliateKeyToCookie($observer->getRequest(), $account->getCode());
                        $redirectUrl = $this->storeManager->getStore()->getBaseUrl();

                        if ($observer->getRequest()->getParam('source') === 'banner') {
                            $url = $this->_helper->getBannerRedirectUrl(
                                $observer->getRequest()->getParam('key')
                            );
                        } else {
                            $url = explode('?', $this->storeManager->getStore()->getCurrentUrl())[0];
                        }

                        if ($url) {
                            $redirectUrl = $url;
                        }
                        $observer->getControllerAction()->getResponse()->setRedirect($redirectUrl);
                    }
                }
                break;
            }
        }

        return $this;
    }

    /**
     * @param $request
     * @param $code
     *
     * @throws InputException
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     */
    public function setAffiliateKeyToCookie($request, $code)
    {
        $this->_helper->setAffiliateKeyToCookie($code);
        if ($source = $request->getParam('source')) {
            $this->_helper->setAffiliateKeyToCookie($source, Data::AFFILIATE_COOKIE_SOURCE_NAME);
            if ($key = $request->getParam('key')) {
                $this->_helper->setAffiliateKeyToCookie($key, Data::AFFILIATE_COOKIE_SOURCE_VALUE);
            }
        } else {
            $this->_helper->deleteAffiliateKeyFromCookie(Data::AFFILIATE_COOKIE_SOURCE_NAME);
        }
    }

    /**
     * Check whether it is possible to set cookie
     *
     * @return bool
     */
    public function checkIsSetCookie()
    {
        $isSetCookie = false;
        if ($this->_helper->getAffiliateKeyFromCookie()) {
            if ($this->_helper->isOverwriteCookies()) {
                $isSetCookie = true;
            }
        } else {
            $isSetCookie = false;
        }

        if (!$isSetCookie && !$this->_helper->getAffiliateKeyFromCookie()) {
            $isSetCookie = true;
        }

        return $isSetCookie;
    }
}
