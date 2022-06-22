<?php

namespace Magenest\RewardPoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class InvoicedAmount implements ObserverInterface
{
    /**
     * @var \Magenest\RewardPoints\Model\LifetimeAmountFactory
     */
    protected $lifetimeAmountFactory;

    /**
     * @var \Magenest\RewardPoints\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magenest\RewardPoints\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * InvoicedAmount constructor.
     * @param \Magenest\RewardPoints\Model\LifetimeAmountFactory $lifetimeAmountFactory
     * @param \Magenest\RewardPoints\Helper\Data $helper
     * @param \Magenest\RewardPoints\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory
     */
    public function __construct(
        \Magenest\RewardPoints\Model\LifetimeAmountFactory $lifetimeAmountFactory,
        \Magenest\RewardPoints\Helper\Data $helper,
        \Magenest\RewardPoints\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory
    )
    {
        $this->lifetimeAmountFactory = $lifetimeAmountFactory;
        $this->helper = $helper;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->getEnableModule()) return;
        if (!$this->isLifetimeAmountRuleInvoicedEnabled()) return;

        $invoice = $observer->getEvent()->getInvoice();
        if ($this->helper->isCheckoutAsGuest($invoice->getOrder())) return;
        if (!$this->helper->canEarnPointsWithDiscountedOrder($invoice->getOrder())) return;

        $customerId = $invoice->getCustomerId();
        if (!$customerId || $invoice->getState() != 2) return;
        $lifetimeAmount = $this->lifetimeAmountFactory->create()->load($customerId, 'customer_id');

        $invoiceIds = $lifetimeAmount->getInvoiceIds();
        $invoiceId = $invoice->getEntityId();
        if (!empty($invoiceIds)) {
            $invoiceIdList = explode(',', $invoiceIds);
            if (in_array($invoiceId, $invoiceIdList)) {
                return;
            }
        }

        $data = [
            'customer_id' => $customerId,
            'invoiced_amount' => $lifetimeAmount->getInvoicedAmount() + $invoice->getBaseSubtotal(),
            'invoice_ids' => $invoiceIds . ',' . $invoiceId,
        ];

        $lifetimeAmount->addData($data);
        $lifetimeAmount->save();
        $this->lifetimeAmountInvoicedAction($invoice);
    }

    /**
     * Lifetime amount rule action based on invoiced amount
     *
     * @param $invoice
     */
    public function lifetimeAmountInvoicedAction($invoice) {
        $this->helper->earnOrderPointsLifetimeAmount($invoice->getOrder());
    }

    /**
     * Check if lifetime amount rule for invoiced amount enabled or not
     *
     * @return bool
     */
    public function isLifetimeAmountRuleInvoicedEnabled() {
        $rule = $this->helper->getLifetimeAmountRule();
        if ($rule === null) return false;
        $lifetimeConfig = $this->helper->getLifetimeAmountConfig($rule);
        if ($lifetimeConfig['type'] != \Magenest\RewardPoints\Helper\Data::INVOICED_AMOUNT) return false;
        return true;
    }

}