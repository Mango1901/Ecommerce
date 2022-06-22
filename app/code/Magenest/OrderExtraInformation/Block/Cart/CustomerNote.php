<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderExtraInformation\Block\Cart;

use Magento\Framework\View\Element\Template;
use Magenest\Core\Helper\Data as CoreData;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Checkout\Model\Session as CheckoutSession;

/**
 * Class LayoutProcessor
 * @package Magenest\OrderExtraInformation\Block\Cart
 */
class CustomerNote extends Template
{
    /**
     * @var CoreData
     */
    protected $_coreData;

    /**
     * @var CheckoutSession
     */
    protected $_checkoutSession;

    /**
     * @var CartRepositoryInterface
     */
    protected $_quoteRepository;

    /**
     * Constructor.
     *
     * @param Template\Context $context
     * @param CoreData $coreData
     * @param CartRepositoryInterface $quoteRepository
     * @param CheckoutSession $checkoutSession
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CoreData $coreData,
        CartRepositoryInterface $quoteRepository,
        CheckoutSession $checkoutSession,
        array $data = []
    )
    {
        $this->_coreData = $coreData;
        $this->_checkoutSession = $checkoutSession;
        $this->_quoteRepository = $quoteRepository;
        parent::__construct($context, $data);
    }

    /**
     * Get customer note
     *
     * @return null|string
     */
    public function getCustomerNote()
    {
        $customerNote = '';
        $quote = $this->_quoteRepository->getActive($this->_checkoutSession->getQuoteId());

        if ($quote->getId()) {
            $customerNote = $quote->getCustomerNote();
        }

        return $customerNote;
    }

    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        if ($this->_coreData->getConfigValue('oei/customer_note/enable')) {
            $showOn = explode(',', $this->_coreData->getConfigValue('oei/customer_note/show_on'));
            if (in_array('cart', $showOn) || in_array('cart_item', $showOn))
                return parent::toHtml();
        }

        return '';
    }
}