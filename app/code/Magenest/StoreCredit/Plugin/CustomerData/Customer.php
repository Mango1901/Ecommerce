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

namespace Magenest\StoreCredit\Plugin\CustomerData;

use Magenest\StoreCredit\Helper\Calculation;

/**
 * Class Customer
 * @package Magenest\StoreCredit\Plugin\CustomerData
 */
class Customer
{
    /**
     * @var Calculation
     */
    protected $helper;

    /**
     * Customer constructor.
     *
     * @param Calculation $helper
     */
    public function __construct(Calculation $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Customer\CustomerData\Customer $subject
     * @param $result
     *
     * @return mixed
     */
    public function afterGetSectionData(\Magento\Customer\CustomerData\Customer $subject, $result)
    {
        $quote = $this->helper->getCheckoutSession()->getQuote();
        $creditData = [];

        if ($this->helper->isEnabled($quote->getStoreId())) {
            $creditData = [
                'isSpendingCredit' => !!floatval($quote->getMpStoreCreditSpent()),
                'balance' => $this->helper->getAccountHelper()->getBalance(),
                'convertedBalance' => $this->helper->convertPrice(
                    $this->helper->getAccountHelper()->getBalance(),
                    true,
                    false
                ),
                'isEnabledFor' => $this->helper->isEnabledForCustomer()
            ];
        }

        return array_merge($result, $creditData);
    }
}
