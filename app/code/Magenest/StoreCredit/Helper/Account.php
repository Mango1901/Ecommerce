<?php
/**
 * Magenest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magenest.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magenest.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magenest
 * @package     Magenest_StoreCredit
 * @copyright   Copyright (c) Magenest (https://www.magenest.com/)
 * @license     https://www.magenest.com/LICENSE.txt
 */

namespace Magenest\StoreCredit\Helper;

use Exception;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magenest\Core\Helper\AbstractData;
use Magenest\StoreCredit\Model\CustomerFactory;

/**
 * Class Account
 * @package Magenest\StoreCredit\Helper
 */
class Account extends AbstractData
{
    /**
     * @var Customer[]
     */
    protected $accountByCustomerId = [];

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * Customer constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param CustomerSession $customerSession
     * @param CustomerRegistry $customerRegistry
     * @param CustomerFactory $customerFactory
     * @param HttpContext $httpContext
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        CustomerSession $customerSession,
        CustomerRegistry $customerRegistry,
        CustomerFactory $customerFactory,
        HttpContext $httpContext,
        Data $helper
    ) {
        $this->customerSession = $customerSession;
        $this->customerRegistry = $customerRegistry;
        $this->customerFactory = $customerFactory;
        $this->httpContext = $httpContext;
        $this->helper = $helper;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @return Customer|null
     */
    public function getCurrentCustomer()
    {
        return $this->getCustomerById($this->getCustomerSession()->getId());
    }

    /**
     * @return CustomerSession
     */
    public function getCustomerSession()
    {
        return $this->customerSession;
    }

    /**
     * @param $customerId
     *
     * @return Customer
     */
    public function getCustomerById($customerId)
    {
        try {
            if (!$customerId) {
                $customerId = $this->getCustomerSession()->getId();
            }

            if (!isset($this->accountByCustomerId[$customerId])) {
                $this->accountByCustomerId[$customerId] = $this->customerRegistry->retrieve($customerId);
            }

            return $this->accountByCustomerId[$customerId];
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param $email
     *
     * @return Customer
     */
    public function getCustomerByEmail($email)
    {
        try {
            return $this->customerRegistry->retrieveByEmail($email);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param null $customerId
     *
     * @return int
     */
    public function getBalance($customerId = null)
    {
        if ($customer = $this->getCustomerById($customerId)) {
            return floatval($customer->getMpCreditBalance());
        }

        return 0;
    }

    /**
     * @param null $customerId
     * @param bool $includeContainer
     * @param null $scope
     * @param int $precision
     *
     * @return int
     */
    public function getFormattedBalance(
        $customerId = null,
        $includeContainer = false,
        $scope = null,
        $precision = PriceCurrencyInterface::DEFAULT_PRECISION
    ) {
        $customer = $this->getCustomerById($customerId);
        $currency = $customer ? $customer->getStore()->getBaseCurrency() : null;

        return $this->helper->formatPrice(
            $this->getBalance($customerId),
            $includeContainer,
            $scope,
            $currency,
            $precision
        );
    }

    /**
     * @param null $price
     * @param null $customerId
     * @param bool $includeContainer
     * @param null $scope
     * @param int $precision
     *
     * @return int
     * @throws LocalizedException
     */
    public function getConvertAndFormatBalance(
        $price = null,
        $customerId = null,
        $includeContainer = false,
        $scope = null,
        $precision = PriceCurrencyInterface::DEFAULT_PRECISION
    ) {
        $customer = $this->getCustomerById($customerId);
        $currency = $customer ? $customer->getStore()->getCurrentCurrency() : null;
        $price = $price ?: $this->getBalance($customerId);

        return $this->helper->convertAndFormatPrice(
            $price,
            $includeContainer,
            $scope,
            $currency,
            $precision
        );
    }

    /**
     * Checking customer login status
     *
     * @return bool
     */
    public function isCustomerLoggedIn()
    {
        return $this->_request->isAjax() ? $this->getCustomerSession()->isLoggedIn()
            : (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }
}
