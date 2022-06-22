<?php

namespace Magenest\SellOnInstagram\Controller\Checkout;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Helper\Cart as CartHelper;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Checkout\Model\Session;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Quote\Model\QuoteRepository;


class AddToCart extends Action
{
    const DEFAULT_QTY = 1;
    const STATUS_FROM_SHOP = 1;
    /**
     * @var FormKey
     */
    protected $formKey;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var CartHelper
     */
    protected $cartHelper;
    /**
     * @var Session
     */
    protected $session;
    /**
     * @var CustomerSession
     */
    protected $customerSession;
    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;

    public function __construct(
        Context $context,
        FormKey $formKey,
        Cart $cart,
        ProductRepository $productRepository,
        CartHelper $cartHelper,
        RedirectFactory $resultRedirectFactory,
        Session $session,
        CustomerSession $customerSession,
        QuoteRepository $quoteRepository
    )
    {
        $this->formKey = $formKey;
        $this->cart = $cart;
        $this->productRepository = $productRepository;
        $this->cartHelper = $cartHelper;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->session = $session;
        $this->customerSession = $customerSession;
        $this->quoteRepository = $quoteRepository;
        parent::__construct($context);
    }

    /**
     * Addcart action
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        try {
            $productId = $this->getRequest()->getParam('product');
            $params = [
                'form_key' => $this->formKey->getFormKey(),
                'product' => $productId,
                'qty' => self::DEFAULT_QTY,
                'from_shop' => self::STATUS_FROM_SHOP
            ];
            $product = $this->productRepository->getById($productId);
            $this->cart->addProduct($product, $params);
            $this->cart->save();
            $quoteId = $this->cart->getQuote()->getId();
            $this->session->setQuoteId($quoteId);
            $quote = $this->session->getQuote();
            $quoteItems = $quote->getAllVisibleItems();
            foreach ($quoteItems as $quoteItem) {
                $quoteItem->setFromShop(self::STATUS_FROM_SHOP);
                $quoteItem->save();
            }
            $this->messageManager->addSuccessMessage(__($product->getName() . ' added to cart successfully.'));
        } catch (\Exception $e) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $this->messageManager->addExceptionMessage(
                $e,
                __('%1', $e->getMessage())
            );
            return $resultRedirect->setPath($product->getProductUrl());
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $cartUrl = $this->cartHelper->getCartUrl();
        return $resultRedirect->setPath($cartUrl);
    }
}
