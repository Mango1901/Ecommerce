<?php

namespace Magenest\RewardPoints\Controller\Quote;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Remove
 *
 * @package Magenest\RewardPoints\Controller\Quote
 */
class Remove extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $cartHelper;

    /**
     * Remove constructor.
     *
     * @param Context $context
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     */
    public function __construct(
        Context $context,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Helper\Cart $cartHelper
    ) {
        parent::__construct($context);
        $this->cart            = $cart;
        $this->cartHelper      = $cartHelper;
        $this->checkoutSession = $checkoutSession;

    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $this->removePointIntoQuote();
        $this->cart->getQuote()->collectTotals()->save();

        return $resultRedirect->setData([]);
    }

    /**
     * @throws \Exception
     */
    private function removePointIntoQuote()
    {
        $quote = $this->checkoutSession->getQuote();
        $quote->setData('reward_point', 0)->save();
        $quote->setData('reward_amount', 0)->save();
        $quote->setData('base_reward_amount', 0)->save();
    }
}
