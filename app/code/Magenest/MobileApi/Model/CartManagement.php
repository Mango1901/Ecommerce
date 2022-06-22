<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MobileApi\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Quote\Model\QuoteRepository;
use Magento\Quote\Model\QuoteManagement;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Sales\Model\OrderRepository;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Checkout\Model\PaymentInformationManagement;
use Magento\Framework\App\ResourceConnection;
use Magento\Checkout\Model\GuestPaymentInformationManagement;
use Magento\Quote\Model\GuestCart\GuestCartManagement;
use Magento\Quote\Model\Quote;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Payment\Model\MethodList;
use Magento\Framework\DataObjectFactory;
use Magento\Vault\Model\CustomerTokenManagement;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class CartManagement
 * @package Magenest\MobileApi\Model
 */
class CartManagement implements \Magenest\MobileApi\Api\CartManagementInterface
{
    /**
     * @var QuoteRepository
     */
    protected $_quoteRepository;

    /**
     * @var QuoteManagement
     */
    protected $_quoteManagement;

    /**
     * @var QuoteIdMaskFactory
     */
    protected $_quoteIdMaskFactory;

    /**
     * @var CartManagementInterface
     */
    protected $_cartManagement;

    /**
     * @var GuestCartManagement
     */
    protected $_guestCartManagement;

    /**
     * @var OrderRepository
     */
    protected $_orderRepository;

    /**
     * @var CheckoutSession
     */
    protected $_checkoutSession;

    /**
     * @var PaymentInformationManagement
     */
    protected $_paymentInformationManagement;

    /**
     * @var GuestPaymentInformationManagement
     */
    protected $_guestPaymentInformationManagement;

    /**
     * @var ResourceConnection
     */
    protected $_connectionPool;

    /**
     * @var ProductRepositoryInterface
     */
    protected $_productRepository;

    /**
     * @var MethodList
     */
    protected $_methodList;

    /**
     * @var DataObjectFactory
     */
    protected $_dataObjectFactory;

    /**
     * @var CustomerTokenManagement
     */
    protected $_customerTokenManagement;

    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Constructor.
     *
     * @param QuoteRepository $quoteRepository
     * @param QuoteManagement $quoteManagement
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param CartManagementInterface $cartManagement
     * @param GuestCartManagement $guestCartManagement
     * @param OrderRepository $orderRepository
     * @param CheckoutSession $checkoutSession
     * @param PaymentInformationManagement $paymentInformationManagement
     * @param GuestPaymentInformationManagement $guestPaymentInformationManagement
     * @param ProductRepositoryInterface $productRepository
     * @param MethodList $methodList
     * @param DataObjectFactory $dataObjectFactory
     * @param CustomerTokenManagement $customerTokenManagement
     * @param CustomerSession $customerSession
     * @param ScopeConfigInterface $scopeConfig
     * @param ResourceConnection $connectionPool
     */
    function __construct(
        QuoteRepository $quoteRepository,
        QuoteManagement $quoteManagement,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        CartManagementInterface $cartManagement,
        GuestCartManagement $guestCartManagement,
        OrderRepository $orderRepository,
        CheckoutSession $checkoutSession,
        PaymentInformationManagement $paymentInformationManagement,
        GuestPaymentInformationManagement $guestPaymentInformationManagement,
        ProductRepositoryInterface $productRepository,
        MethodList $methodList,
        DataObjectFactory $dataObjectFactory,
        CustomerTokenManagement $customerTokenManagement,
        CustomerSession $customerSession,
        ScopeConfigInterface $scopeConfig,
        ResourceConnection $connectionPool = null
    )
    {
        $this->_quoteRepository = $quoteRepository;
        $this->_quoteManagement = $quoteManagement;
        $this->_quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->_cartManagement = $cartManagement;
        $this->_guestCartManagement = $guestCartManagement;
        $this->_orderRepository = $orderRepository;
        $this->_checkoutSession = $checkoutSession;
        $this->_paymentInformationManagement = $paymentInformationManagement;
        $this->_guestPaymentInformationManagement = $guestPaymentInformationManagement;
        $this->_productRepository = $productRepository;
        $this->_methodList = $methodList;
        $this->_dataObjectFactory = $dataObjectFactory;
        $this->_customerTokenManagement = $customerTokenManagement;
        $this->_customerSession = $customerSession;
        $this->_scopeConfig = $scopeConfig;
        $this->_connectionPool = $connectionPool ?: ObjectManager::getInstance()->get(ResourceConnection::class);
    }

    /**
     * @inheritdoc
     */
    public function mergeGuestCart($customerId, $guestQuoteId)
    {
        try {
            $quote = $this->_quoteRepository->getActiveForCustomer($customerId);
        } catch (\Exception $e) {
            $quoteId = $this->_quoteManagement->createEmptyCartForCustomer($customerId);
            $quote = $this->_quoteRepository->getActive($quoteId);
        }

        $quoteIdMask = $this->_quoteIdMaskFactory->create()->load($guestQuoteId, 'masked_id');
        $guestQuote = $this->_quoteRepository->getActive($quoteIdMask->getQuoteId());
        $quote->merge($guestQuote)
            ->setTotalsCollectedFlag(false)
            ->collectTotals();

        $this->_quoteRepository->save($quote);
        $this->_quoteRepository->delete($guestQuote);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function savePaymentInformationAndPlaceOrder($cartId, PaymentInterface $paymentMethod, AddressInterface $billingAddress = null)
    {
        try {
            $this->_paymentInformationManagement->savePaymentInformation($cartId, $paymentMethod, $billingAddress);
            $orderId = $this->_cartManagement->placeOrder($cartId);
        } catch (LocalizedException $e) {
            throw new CouldNotSaveException(__($e->getMessage()), $e);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('A server error stopped your order from being placed. Please try to place your order again.'), $e);
        }

        return $this->_orderRepository->get($orderId);
    }

    /**
     * @inheritdoc
     */
    public function savePaymentInformationAndPlaceGuestOrder($cartId, $email, PaymentInterface $paymentMethod, AddressInterface $billingAddress = null)
    {
        $salesConnection = $this->_connectionPool->getConnection('sales');
        $checkoutConnection = $this->_connectionPool->getConnection('checkout');
        $salesConnection->beginTransaction();
        $checkoutConnection->beginTransaction();

        try {
            try {
                $this->_guestPaymentInformationManagement->savePaymentInformation($cartId, $email, $paymentMethod, $billingAddress);
                $orderId = $this->_guestCartManagement->placeOrder($cartId);
            } catch (LocalizedException $e) {
                throw new CouldNotSaveException(__($e->getMessage()), $e);
            } catch (\Exception $e) {
                throw new CouldNotSaveException(__('An error occurred on the server. Please try to place the order again.'), $e);
            }

            $salesConnection->commit();
            $checkoutConnection->commit();
        } catch (\Exception $e) {
            $salesConnection->rollBack();
            $checkoutConnection->rollBack();
            throw $e;
        }

        return $this->_orderRepository->get($orderId);
    }

    /**
     * @inheritdoc
     */
    public function guestItemProduct($cartId, $itemId)
    {
        $quoteIdMask = $this->_quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $this->itemProduct($quoteIdMask->getQuoteId(), $itemId);
    }

    /**
     * @inheritdoc
     */
    public function itemProduct($cartId, $itemId)
    {
        $quote = $this->_quoteRepository->getActive($cartId);
        $item = $quote->getItemById($itemId);
        if (!$item) {
            throw new LocalizedException(__('Item was found from cart.'));
        }

        return $this->_productRepository->getById($item->getProductId());
    }

    /**
     * @inheritdoc
     */
    public function getGuestPaymentMethodList($cartId)
    {
        $quoteIdMask = $this->_quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $this->getPaymentMethodList($quoteIdMask->getQuoteId());
    }

    /**
     * @inheritdoc
     */
    public function getPaymentMethodList($cartId)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->_quoteRepository->get($cartId);
        if ($quote->getCustomerId()) {
            $this->_initCustomerSession($quote->getCustomerId());
        }

        $availableMethods = [];
        foreach ($this->_methodList->getAvailableMethods($quote) as $method) {
            $availableMethods[$method->getCode()] = [
                'code' => $method->getCode(),
                'title' => $method->getTitle(),
                'description' => $method->getConfigData('instructions')
            ];
        }

        if (isset($availableMethods['alepay_atm'])) {
            $availableMethods['alepay_atm']['allowedMethodCode'] = $this->_scopeConfig->getValue('payment/alepay_atm/specific_method_code', ScopeInterface::SCOPE_STORE);
        }

        return $this->_dataObjectFactory->create()
            ->addData($availableMethods)
            ->getData();
    }

    /**
     * Init customer session
     *
     * @param int $customerId
     */
    private function _initCustomerSession($customerId)
    {
        $this->_customerSession->setCustomerId($customerId);
    }
}
