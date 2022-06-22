<?php

namespace Magenest\RewardPoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class FirstPurchaseMessage implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magenest\RewardPoints\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magenest\RewardPoints\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * FirstPurchaseMessage constructor.
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magenest\RewardPoints\Helper\Data $helper
     * @param \Magenest\RewardPoints\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magenest\RewardPoints\Helper\Data $helper,
        \Magenest\RewardPoints\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->messageManager = $messageManager;
        $this->checkoutSession = $checkoutSession;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->helper = $helper;
        $this->customerSession = $customerSession;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->customerSession->isLoggedIn() && $this->helper->isLoginNoficationEnabled())
            $this->messageManager->addWarningMessage(__('Please login or create an account to have opportunities to receive bonus points when buying at our store.'));

        $quote = $this->checkoutSession->getQuote();
        if ($this->helper->validateFirstOrder($quote, 0)) {

            $rules = $this->ruleCollectionFactory->create()->addFieldToFilter('condition', 'firstpurchase');
            if (count($rules->getData())) {
                $currencySymbol = $this->helper->getDefaultCurrencySymbol();
                $count = 0;

                $rules = $rules->getItems();
                if (count($rules) > 1) {
                    usort($rules, [$this, "cmp"]);
                }

                foreach ($rules as $rule) {
                    if (!$this->helper->validateRule($rule)) continue;
                    $count++;
                    $point = $rule->getPoints();
                    $amount = $rule->getConditions()->getConditions()[0]->getValue();
                    if ($point) {
                        if ($count == 1) {
                            $this->messageManager->addSuccessMessage(__('This is your first purchase.'));
                            $msg = "If your first purchase is more than %s, you will receive %s points.";
                        } else {
                            $msg = "If your first purchase is more than %s, you will receive %s more points.";
                        }
                        $msg = sprintf(__($msg), $currencySymbol . number_format($amount, 2), $point);
                        $this->messageManager->addSuccessMessage($msg);
                    }
                }
            }
        }

    }

    /**
     * Sort rules by ascending amount
     *
     * @param $rule1
     * @param $rule2
     * @return int
     */
    public function cmp($rule1, $rule2)
    {
        $amount1 = $rule1->getConditions()->getConditions()[0]->getValue();
        $amount2 = $rule2->getConditions()->getConditions()[0]->getValue();

        if ($amount1 == $amount2) {
            return 0;
        }
        return ($amount1 < $amount2) ? -1 : 1;
    }
}