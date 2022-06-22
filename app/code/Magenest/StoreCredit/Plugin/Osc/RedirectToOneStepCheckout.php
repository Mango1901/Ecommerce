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

namespace Magenest\StoreCredit\Plugin\Osc;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Closure;
use Magento\Framework\Event\Observer;
use Magento\Customer\Model\Session as CustomerSession;
use Magenest\StoreCredit\Helper\Data as StoreCreditHelper;

/**
 * Class RedirectToOneStepCheckout
 * @package Magenest\StoreCredit\Plugin\Osc
 */
class RedirectToOneStepCheckout
{
    /**
     * @var StoreCreditHelper
     */
    private $helper;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * RedirectToOneStepCheckout constructor.
     * @param StoreCreditHelper $helper
     * @param Session $checkoutSession
     * @param CustomerSession $customerSession
     */
    public function __construct(
        StoreCreditHelper $helper,
        Session $checkoutSession,
        CustomerSession $customerSession
    ){
        $this->helper          = $helper;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession  = $customerSession;
    }

    /**
     * @param \Magenest\Osc\Observer\RedirectToOneStepCheckout $subject
     * @param Closure $proceed
     * @param Observer $observer
     * @return mixed|null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function aroundExecute(
        \Magenest\Osc\Observer\RedirectToOneStepCheckout $subject,
        Closure $proceed,
        Observer $observer
    ){
        if (!$this->helper->isEnabled() || $this->customerSession->isLoggedIn()) {
            return $proceed($observer);
        }

        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getData('product');

        if ($product->getId() && $product->getTypeId() === 'mpstorecredit') {
            return null;
        }

        $quote = $this->checkoutSession->getQuote();
        if (!$quote->getId() || !$quote->getItems()) {
            return $proceed($observer);
        }

        foreach ($quote->getItems() as $item) {
            if ($item->getProductType() === 'mpstorecredit') {
                return null;
            }
        }

        return $proceed($observer);
    }
}
