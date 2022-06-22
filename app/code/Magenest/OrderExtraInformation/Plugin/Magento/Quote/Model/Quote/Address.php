<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Kangaroo extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Kangaroo
 */

namespace Magenest\OrderExtraInformation\Plugin\Magento\Quote\Model\Quote;

use Magento\Quote\Model\Quote\Address\Rate;

class Address
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    protected $_isProcessed = false;

    /**
     * Address constructor.
     *
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->_request = $request;
    }

    public function afterGetShippingRatesCollection(\Magento\Quote\Model\Quote\Address $address, $result)
    {
        if (!$this->inShippingChangeRequest() || $this->_isProcessed) {
            return $result;
        }
        $adjustmentAmount = (float)$this->_request->getParam('shipping_fee', 0);
        /** @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $result */
        foreach ($result as $rate) {
            /** @var Rate $rate */
            $amount = ($rate->getPrice() + $adjustmentAmount > 0) ? ($rate->getPrice() + $adjustmentAmount) : 0;
            $rate->setPrice($amount);
        }
        $this->_isProcessed = true;

        return $result;
    }

    private function inShippingChangeRequest()
    {
        $adjustmentAmount = $this->_request->getParam('shipping_fee', false);

        return (boolean)$adjustmentAmount;
    }
}
