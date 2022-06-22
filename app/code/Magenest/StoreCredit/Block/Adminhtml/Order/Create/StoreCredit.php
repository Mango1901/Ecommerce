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

namespace Magenest\StoreCredit\Block\Adminhtml\Order\Create;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Session\Quote;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate;
use Magento\Sales\Model\AdminOrder\Create;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magenest\StoreCredit\Helper\Calculation;

/**
 * Class StoreCredit
 *
 * @package Magenest\StoreCredit\Block\Adminhtml\Order\Create
 */
class StoreCredit extends AbstractCreate
{
    /**
     * @var Calculation
     */
    protected $helper;

    /**
     * StoreCredit constructor.
     *
     * @param Context                $context
     * @param Quote                  $sessionQuote
     * @param Create                 $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param Calculation            $helper
     * @param array                  $data
     */
    public function __construct(
        Context $context,
        Quote $sessionQuote,
        Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        Calculation $helper,
        array $data = []
    ) {
        $this->helper = $helper;

        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $data);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->helper->isEnabled($this->getStoreId());
    }

    /**
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function isEnabledSpending()
    {
        return $this->helper->isEnabledSpending($this->getStoreId(), $this->getCustomerId()) && $this->getBalance();
    }

    /**
     * @return float
     */
    public function isSpendingCredit()
    {
        return floatval($this->getQuote()->getMpStoreCreditSpent());
    }

    /**
     * @return int
     */
    public function getBalance()
    {
        return $this->helper->getAccountHelper()->getBalance($this->getCustomerId());
    }

    /**
     * @return int
     * @throws LocalizedException
     */
    public function getConvertAndFormatBalance()
    {
        return $this->helper->getAccountHelper()->getConvertAndFormatBalance(null, $this->getCustomerId());
    }
}
