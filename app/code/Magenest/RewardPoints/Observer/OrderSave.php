<?php

namespace Magenest\RewardPoints\Observer;

use Magento\Backend\App\ConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magenest\RewardPoints\Helper\Data;

/**
 * Class OrderSave
 * @package Magenest\RewardPoints\Observer
 */
class OrderSave implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $_helper;

    /**
     * @var ConfigInterface
     */
    protected $_config;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $_cacheFrontendPool;

    /**
     * @var \Magenest\RewardPoints\Model\LifetimeAmountFactory
     */
    protected $lifetimeAmountFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * OrderSave constructor.
     * @param Data $helper
     * @param ConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magenest\RewardPoints\Model\LifetimeAmountFactory $lifetimeAmountFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     */
    public function __construct(
        Data $helper,
        ConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magenest\RewardPoints\Model\LifetimeAmountFactory $lifetimeAmountFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->_config = $config;
        $this->_helper = $helper;
        $this->lifetimeAmountFactory = $lifetimeAmountFactory;
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        if ($this->_helper->getEnableModule()) {
            $validStatuses = $this->_helper->getRewardPointsWhen();
            $statuses = explode(',', $validStatuses);
            /** @var \Magento\Sales\Model\Order $order */
            $order = $observer->getEvent()->getOrder();
            if ($this->_helper->isCheckoutAsGuest($order)) {
                return;
            }
            if ($this->isLifetimeAmountRuleOrderedEnabled()) {
                $this->lifetimeAmountAction($order);
            }
            if ($order->getcustomerId() != null && in_array($order->getStatus(), $statuses)) {
                $this->_helper->earnOrderPoints($order);
                $this->_helper->earnOrderPointsBehavior($order);
                $this->cleanFullPageCache();
            }

            if ($order->getState() == \Magento\Sales\Model\Order::STATE_CANCELED) {
                //todo
            }
        }
    }

    public function cleanFullPageCache()
    {
        $this->_cacheTypeList->cleanType('full_page');
    }

    /**
     * @param $order
     * @throws \Exception
     */
    public function lifetimeAmountAction($order)
    {
        if (!$this->_helper->canEarnPointsWithDiscountedOrder($order)) {
            return;
        }
        $customerId = $order->getCustomerId();
        if (!$customerId) {
            return;
        }
        $lifetimeAmount = $this->lifetimeAmountFactory->create()->load($customerId, 'customer_id');

        $orderIds = $lifetimeAmount->getOrderIds();
        $orderId = $order->getEntityId();
        if (!empty($orderIds)) {
            $orderIdList = explode(',', $orderIds);
            if (in_array($orderId, $orderIdList)) {
                return;
            }
        }

        $data = [
            'customer_id' => $customerId,
            'ordered_amount' => $lifetimeAmount->getOrderedAmount() + $order->getBaseSubtotal(),
            'order_ids' => $orderIds . ',' . $orderId,
        ];

        $lifetimeAmount->addData($data);
        $lifetimeAmount->save();
    }

    /**
     * Check if lifetime amount rule for ordered amount enabled or not
     *
     * @return bool
     */
    public function isLifetimeAmountRuleOrderedEnabled()
    {
        $rule = $this->_helper->getLifetimeAmountRule();
        if ($rule === null) {
            return false;
        }
        $lifetimeConfig = $this->_helper->getLifetimeAmountConfig($rule);
        if ($lifetimeConfig['type'] != \Magenest\RewardPoints\Helper\Data::ORDERED_AMOUNT) {
            return false;
        }
        return true;
    }
}
