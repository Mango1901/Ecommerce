<?php

namespace Magenest\RewardPoints\Observer;

use Magento\Backend\App\ConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magenest\RewardPoints\Model\AccountFactory;
use Magenest\RewardPoints\Model\TransactionFactory;
use Magenest\RewardPoints\Model\ExpiredFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magenest\RewardPoints\Model\RuleFactory;
use Magento\CatalogRule\Model\RuleFactory as CoreRuleFactory;
use Magenest\RewardPoints\Helper\Data;

/**
 * Class PlaceOrder
 * @package Magenest\RewardPoints\Observer
 */
class PlaceOrder implements ObserverInterface
{
    /**
     * @var AccountFactory
     */
    protected $_accountFactory;

    /**
     * @var TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var ExpiredFactory
     */
    protected $expiredFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var RuleFactory
     */
    protected $_ruleFactory;

    /**
     * @var CoreRuleFactory
     */
    protected $_coreRuleFactory;

    /**
     * @var Data
     */
    protected $_helper;

    /**
     * @var
     */
    protected $_order;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var
     */
    protected $_quote;

    /**
     * @var ConfigInterface
     */
    protected $_config;

    /**
     * PlaceOrder constructor.
     *
     * @param AccountFactory $accountFactory
     * @param ConfigInterface $config
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param RuleFactory $ruleFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param StoreManagerInterface $storeManagerInterface
     * @param CoreRuleFactory $coreRuleFactory
     * @param Data $helper
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param TransactionFactory $transactionFactory
     * @param ExpiredFactory $expiredFactory
     */
    public function __construct(
        AccountFactory $accountFactory,
        ConfigInterface $config,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        RuleFactory $ruleFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        StoreManagerInterface $storeManagerInterface,
        CoreRuleFactory $coreRuleFactory,
        Data $helper,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        TransactionFactory $transactionFactory,
        ExpiredFactory $expiredFactory
    ) {
        $this->_accountFactory     = $accountFactory;
        $this->_config             = $config;
        $this->quoteFactory        = $quoteFactory;
        $this->_transactionFactory = $transactionFactory;
        $this->messageManager      = $messageManager;
        $this->_ruleFactory        = $ruleFactory;
        $this->_orderFactory       = $orderFactory;
        $this->_coreRuleFactory    = $coreRuleFactory;
        $this->_storeManager       = $storeManagerInterface;
        $this->_helper             = $helper;
        $this->expiredFactory      = $expiredFactory;
    }

    /**
     * @param Observer $observer
     *
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        if ($this->_helper->getEnableModule()) {
            /**
             * @var \Magento\Sales\Model\Order $order
             */
            $order          = $observer->getEvent()->getOrder();
            $order_id       = $order->getId();
            $increment_id = $order->getData('increment_id');
            $customerId     = $order->getCustomerId();
            $origOderStatus = $order->getOrigData('order_status');
            if ($origOderStatus == null && $customerId != null) {
                $quoteId      = $order->getQuoteId();
                $this->_quote = $this->quoteFactory->create()->load($quoteId);
                $allShippingAddresses = $this->_quote->getAllShippingAddresses();
                $quotePoint   = $this->_quote->getData('reward_point')*1 / count($allShippingAddresses);
                $rewardAmount = $this->_quote->getData('reward_amount') / count($allShippingAddresses);
                if ($quotePoint) {
                    /**
                     * add rewardpoint to order from quote
                     */
                    if (isset($this->_quote)) {
                        $dataToSaveOrder = [
                            'reward_point' => $quotePoint,
                            'reward_amount' => $rewardAmount
                        ];
                        $order->addData($dataToSaveOrder);
                        $order->save();
                    }

                    /**
                     * end add
                     */
                    $comment      = 'Order #: ' . $increment_id;
                    $accountModel = $this->_accountFactory->create();
                    $account      = $accountModel->getCollection()->addFieldToFilter('customer_id', $customerId)->getFirstItem();
                    if ($account->getId()) {
                        $accountModel->load($account->getId());
                    }
                    $data = [
                        'customer_id'    => $customerId,
                        'points_spent'   => $account->getData('points_spent') + $quotePoint,
                        'points_current' => $account->getData('points_current') - $quotePoint,
                    ];
                    $accountModel->addData($data)->save();


                    $expiredModel    = $this->expiredFactory->create();
                    $listPointOfUser = $expiredModel->getCollection()
                        ->addFieldToFilter('customer_id', $customerId)
                        ->addFieldToFilter('status', 'available')
                        ->setOrder('expired_date', 'DESC')->getData();
                    $neededPoint     = $quotePoint;
                    $expiredId = '';
                    if (is_array($listPointOfUser)) {
                        foreach ($listPointOfUser as $userPoint) {
                            if ($neededPoint > 0) {
                                $pointId = $userPoint['id'];
                                $details = $expiredModel->load($pointId);
                                $detail  = $details->getData();
                                if ($neededPoint > $detail['points_change']) {
                                    $neededPoint      -= $detail['points_change'];
                                    $detail['status'] = 'used';
                                    $details->setData($detail)->save();
                                    $expiredId = $expiredId . ',' . $userPoint['id'];
                                } else {
                                    $detail['points_change'] -= $neededPoint;
                                    $details->setData($detail)->save();
                                    $expiredId = $expiredId . ',' . $userPoint['id'];
                                    break;
                                }
                            } else {
                                break;
                            }
                        }
                    } else {
                        $pointId                 = $listPointOfUser['id'];
                        $details                 = $expiredModel->load($pointId);
                        $detail                  = $details->getData();
                        $detail['points_change'] -= $neededPoint;
                        $details->setData($detail)->save();
                    }

                    $transactionModel = $this->_transactionFactory->create();
                    $data             = [
                        'order_id'      => $order_id,
                        'customer_id'   => $customerId,
                        'points_change' => -$quotePoint,
                        'points_after'  => $accountModel->getData('points_current'),
                        'comment'       => $comment,
                        'expired_id'    => $expiredId
                    ];
                    $transactionModel->addData($data)->save();

                    //Send email
                    if ($this->_helper->getBalanceEmailEnable()) {
                        $this->_helper->getSendEmail($order, $account, null, null, null, null);
                    }
                } else {
                    if (isset($this->_quote)) {
                        $dataToSaveOrder = [
                            'reward_tier' => $this->_quote->getData('reward_tier'),
                            'reward_amount' => $rewardAmount
                        ];
                        $order->addData($dataToSaveOrder);
                        $order->save();
                    }
                }
            }
        }
    }
}
