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

namespace Magenest\StoreCredit\Block\Adminhtml\Order\Creditmemo\Create;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order\Creditmemo;
use Magenest\StoreCredit\Helper\Calculation;

/**
 * Class StoreCredit
 * @package Magenest\StoreCredit\Block\Adminhtml\Order\Creditmemo\Create
 */
class StoreCredit extends Template
{
    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var Calculation
     */
    protected $helper;

    /**
     * StoreCredit constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param Calculation $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Calculation $helper,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->helper = $helper;

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getInputName()
    {
        if ($this->isAllowRefundExchange()) {
            return 'mpstorecredit[refund_exchange]';
        }

        return 'mpstorecredit[refund_credit]';
    }

    /**
     * @return float
     */
    public function getRefundAmount()
    {
        $creditmemo = $this->getCreditmemo();

        $credit = 0;

        if ($this->isAllowRefundSpending()) {
            $credit += $creditmemo->getMpStoreCreditBaseDiscount();
        }

        if ($this->isAllowRefundExchange()) {
            $credit += $creditmemo->getBaseGrandTotal();
        }

        return (float)$credit;
    }

    /**
     * @return float
     */
    public function getMinCredit()
    {
        return $this->isAllowRefundSpending() ? (float)$this->getCreditmemo()->getMpStoreCreditBaseDiscount() : 0;
    }

    /**
     * @return bool
     */
    public function isAllowRefund()
    {
        return ($this->isAllowRefundExchange() || $this->isAllowRefundSpending()) && $this->getRefundAmount();
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->helper->isEnabled($this->getCreditmemo()->getStoreId());
    }

    /**
     * @return bool
     */
    public function isAllowRefundSpending()
    {
        $storeId = $this->getCreditmemo()->getStoreId();

        return $this->helper->isAllowRefundSpending($storeId);
    }

    /**
     * @return bool
     */
    public function isAllowRefundExchange()
    {
        $storeId = $this->getCreditmemo()->getStoreId();

        return $this->helper->isAllowRefundExchange($storeId);
    }

    /**
     * Retrieve credit memo model instance
     *
     * @return Creditmemo
     */
    public function getCreditmemo()
    {
        return $this->_coreRegistry->registry('current_creditmemo');
    }
}
