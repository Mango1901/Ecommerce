<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_RewardPoints extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_RewardPoints
 */

namespace Magenest\RewardPoints\Observer\Creditmemo;

use Magenest\RewardPoints\Helper\Data;
use Magenest\RewardPoints\Model\Rule;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Store\Model\ScopeInterface;

/**
 * Magenest RewardPoints Module Observer.
 * used for event: sales_order_creditmemo_save_after
 */
class CreditmemoSaveAfter implements ObserverInterface
{
	/**
	 * @var string
	 */
	private static $messageRefundToGiftCard = "We refunded %1 to Reward Point (%2)";

	private static $messageDeductPointRefund = "Points voided at order #%1 refund.";

	private static $messageReturnPointRefund = "Return applied reward points for order #%1 when refund.";
	/**
	 * @var \Magenest\RewardPoints\Model\AccountFactory
	 */
	protected $_accountFactory;
	/**
	 * @var \Magenest\RewardPoints\Model\ExpiredFactory
	 */
	protected $_expiredFactory;
	/**
	 * @var \Magenest\RewardPoints\Model\TransactionFactory
	 */
	protected $_transactionFactory;
	/**
	 * @var Data
	 */
	protected $_hlp;
	/**
	 * @var ScopeConfigInterface
	 */
	private $scopeConfig;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * CreditmemoSaveAfter constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magenest\RewardPoints\Model\AccountFactory $accountFactory
     * @param \Magenest\RewardPoints\Model\ExpiredFactory $expiredFactory
     * @param \Magenest\RewardPoints\Model\TransactionFactory $transactionFactory
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param Data $hlp
     */
	public function __construct(
		ScopeConfigInterface $scopeConfig,
		\Magenest\RewardPoints\Model\AccountFactory $accountFactory,
		\Magenest\RewardPoints\Model\ExpiredFactory $expiredFactory,
		\Magenest\RewardPoints\Model\TransactionFactory $transactionFactory,
		Data $hlp
	) {
		$this->_accountFactory = $accountFactory;
		$this->_expiredFactory = $expiredFactory;
		$this->_transactionFactory = $transactionFactory;
		$this->scopeConfig = $scopeConfig;
		$this->_hlp = $hlp;
	}

	/**
	 * Refunds process.
	 *
	 * @param Observer $observer
	 * @return void
	 * @throws \Exception
	 */
	public function execute(Observer $observer)
	{
		/** @var Creditmemo $creditmemo */
		$creditmemo = $observer->getEvent()->getCreditmemo();

		if ($this->refundToRewardPointsExpected($creditmemo)) {
			$this->refundToRewardPointAccount($creditmemo);
		}

		if ($this->deductRewardAmountWhenRefund($creditmemo)) {
			$this->removePointEarnedAfterRefund($creditmemo);
		}

		if ($this->returnRewardPointToCustomerExpected($creditmemo)) {
			$this->returnRewardPointToCustomer($creditmemo);
		}

		return;
	}

	/**
	 * Checks conditions for refund to reward points.
	 *
	 * @param Creditmemo $creditmemo
	 * @return bool
	 */
	private function refundToRewardPointsExpected(Creditmemo $creditmemo)
	{
		/** @var Order $order */
		$order = $creditmemo->getOrder();
		if (!$creditmemo->getCustomerRewardPointsRefundFlag() || $order->getCustomerIsGuest()) {
			return false;
		}

		// Gets 'Enable Reward Points Functionality' flag from the Scope Config.
		$rewardPointEnable = $this->scopeConfig->getValue(
			'rewardpoints/reward_points_display/reward_points_enable',
			ScopeInterface::SCOPE_STORE
		);

		return (bool)$rewardPointEnable;
	}

	/**
	 * Refunds to reward points if customer is not guest or reward points enabled.
	 *
	 * @param Creditmemo $creditmemo
	 * @return void
	 * @throws \Exception
	 */
	private function refundToRewardPointAccount(Creditmemo $creditmemo)
	{
		/** @var Order $order */
		$order = $creditmemo->getOrder();
		$customerId = $order->getCustomerId();
		$baseRefundAmount = $creditmemo->getBsCustomerMgnRwpTotalRefunded();
		$point = $this->getRewardCurrencyAmount($baseRefundAmount);
		if ($point != 0) {
			$comment = __(
				self::$messageRefundToGiftCard,
				$order->getBaseCurrency()->formatTxt($baseRefundAmount),
				$point
			);
			$accountModel = $this->_accountFactory->create();
			$account = $accountModel->getCollection()->addFieldToFilter('customer_id', $customerId)->getFirstItem();
			if ($account->getId()) {
				$accountModel->load($account->getId());
			}
			$data = [
				'customer_id' => $customerId,
				'points_total' => $account->getData('points_total') + $point,
				'points_current' => $account->getData('points_current') + $point,
			];
			$accountModel->addData($data)->save();

			$transactionModel = $this->_transactionFactory->create();
			$data = [
				'rule_id' => Rule::REFUND_BY_REWARD_POINTS,
				'order_id' => $order->getId(),
				'customer_id' => $customerId,
				'points_change' => $point,
				'points_after' => $accountModel->getData('points_current'),
				'comment' => $comment
			];
			$transactionModel->addData($data)->save();

			$transactionId = $transactionModel->getId();
			/**
			 * @param $expiredModel \Magenest\RewardPoints\Model\ExpiredFactory
			 */
			if ($point > 0) {
				$expiredModel = $this->_expiredFactory->create();
				$timeToExpired = (int)$this->scopeConfig->getValue(Data::XML_PATH_POINT_TIME_EXPIRE, ScopeInterface::SCOPE_STORE);
				$timeExpired = strtotime("+" . $timeToExpired . " days");
				$dateExpired = date("Y-m-d H:i:s", $timeExpired);
				$data = [
					'transaction_id' => $transactionId,
					'rule_id' => Rule::REFUND_BY_REWARD_POINTS,
					'order_id' => $order->getId(),
					'customer_id' => $customerId,
					'points_change' => $point,
					'expired_date' => $dateExpired,
					'status' => 'available',
                    'expiry_type' => (bool)$timeToExpired
				];
                //Send Email Refund
                $rule_id = Rule::REFUND_BY_REWARD_POINTS;
                $this->sendEmailRefund($creditmemo, $point, $account, $rule_id);
				$expiredModel->addData($data)->save();
			}
		}
	}

	/**
	 * @param $point
	 *
	 * @return float|int
	 */
	private function getRewardCurrencyAmount($point)
	{
		$value = $this->scopeConfig->getValue(Data::XML_PATH_POINT_BASE_MONEY, ScopeInterface::SCOPE_STORE);
        $upOrDown = $this->_hlp->getUpOrDown();
        $result = $point * $value;
        if ($upOrDown === 'up') {
            return ceil($result);
        }
		return floor($result);
	}

	/**
	 * @param $creditmemo Creditmemo
	 * @return bool
	 */
	private function deductRewardAmountWhenRefund($creditmemo)
	{
		/** @var Order $order */
		$order = $creditmemo->getOrder();
		if ($order->getCustomerIsGuest()) {
			return false;
		}

		// Gets 'Enable Reward Points Functionality' flag from the Scope Config.
		$rewardPointEnable = $this->scopeConfig->getValue(
			'rewardpoints/reward_points_display/reward_points_enable',
			ScopeInterface::SCOPE_STORE
		);

		$result = $this->scopeConfig->getValue(Data::XML_PATH_DEDUCT_POINT_REFUND, ScopeInterface::SCOPE_STORE);

		return (bool)$result && $rewardPointEnable;
	}

	/**
	 * @param Creditmemo $creditmemo
	 * @throws \Exception
	 */
	private function removePointEarnedAfterRefund($creditmemo)
	{
		$customerId = $creditmemo->getOrder()->getCustomerId();
        $pointTransaction = $this->_transactionFactory->create()->getCollection()
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('order_id', $creditmemo->getOrder()->getId());
        $tableName   = $pointTransaction->getResource()->getTable('magenest_rewardpoints_rule');
		$pointTransaction->getSelect()
            ->join(
		    ['rw_rule' => $tableName],
            'main_table.rule_id = rw_rule.id',
            'action_type' and 'condition'
            )
            ->where('rw_rule.action_type IN (?)', [1, 2]);

		$pointEarned = 0;
		foreach($pointTransaction as $item) {
		    $condition = $item->getCondition();
		    $point = $item->getPointsChange();
            if ($condition == Rule::CONDITION_FIRST_PURCHASE || $condition == Rule::CONDITION_LIFETIME_AMOUNT) {
                $pointEarned -= $item->getRuleId($point);
            } else {
                $pointEarned += $point;
            }
		}

		if ($pointEarned > 0 && $orderAmount = $creditmemo->getOrder()->getBaseGrandTotal() > 0) {
			$creditMemoAmount = $creditmemo->getBaseGrandTotal();
			$orderAmount = $creditmemo->getOrder()->getBaseGrandTotal();
			$upOrDown = $this->_hlp->getUpOrDown();
			$pointDeduct = ($creditMemoAmount / $orderAmount) * $pointEarned;
			if ($upOrDown === 'up') {
				$pointDeduct = ceil($pointDeduct);
			} else {
				$pointDeduct = floor($pointDeduct);
			}

			$comment = __(
				self::$messageDeductPointRefund,
				$creditmemo->getOrder()->getIncrementId()
			);
			$accountModel = $this->_accountFactory->create();
			$account = $accountModel->getCollection()->addFieldToFilter('customer_id', $customerId)->getFirstItem();
			if ($account->getId()) {
				$accountModel->load($account->getId());
			}
			$data = [
				'customer_id' => $customerId,
				'points_current' => $account->getData('points_current') - $pointDeduct,
				'points_spent' => $account->getData('points_spent') + $pointDeduct,
			];
			$accountModel->addData($data)->save();

            $expiredModel    = $this->_expiredFactory->create();
            $listPointOfUser = $expiredModel->getCollection()
                ->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('status', 'available')
                ->setOrder('expired_date', 'ASC')->getData();
            $neededPoint     = $pointDeduct;
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
                        } else {
                            $detail['points_change'] -= $neededPoint;
                            $details->setData($detail)->save();
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
			$data = [
				'rule_id' => Rule::POINT_DEDUCT_BY_REFUND,
				'order_id' => $creditmemo->getOrder()->getId(),
				'customer_id' => $customerId,
				'points_change' => -$pointDeduct,
				'points_after' => $accountModel->getData('points_current'),
				'comment' => $comment
			];
			//Send Email Refund
            $point = -$pointDeduct;
            $rule_id = Rule::POINT_DEDUCT_BY_REFUND;
            $this->sendEmailRefund($creditmemo, $point, $account, $rule_id);
			$transactionModel->addData($data)->save();
		}
	}

	/**
	 * Checks conditions for refund to Gift Card Account.
	 *
	 * @param Creditmemo $creditmemo
	 * @return bool
	 */
	private function returnRewardPointToCustomerExpected(Creditmemo $creditmemo)
	{
		/** @var Order $order */
		$order = $creditmemo->getOrder();
		if (!$creditmemo->getReturnRewardPointToCustomerFlag() || $order->getCustomerIsGuest()) {
			return false;
		}

		// Gets 'Enable Reward Points Functionality' flag from the Scope Config.
		$rewardPointEnable = $this->scopeConfig->getValue(
			'rewardpoints/reward_points_display/reward_points_enable',
			ScopeInterface::SCOPE_STORE
		);

		return (bool)$rewardPointEnable;
	}

    /**
     * @param Creditmemo $creditmemo
     * @throws \Exception
     */
    private function returnRewardPointToCustomer(Creditmemo $creditmemo) {
        $customerId = $creditmemo->getOrder()->getCustomerId();
        $pointAmount = $creditmemo->getReturnRewardPointToCustomerAmount();

        $comment = __(
            self::$messageReturnPointRefund,
            $creditmemo->getOrder()->getIncrementId()
        );
        $accountModel = $this->_accountFactory->create();
        $account = $accountModel->getCollection()->addFieldToFilter('customer_id', $customerId)->getFirstItem();
        if ($account->getId()) {
            $accountModel->load($account->getId());
        }
        $data = [
            'customer_id' => $customerId,
            'points_current' => $account->getData('points_current') + $pointAmount,
            'points_total' => $account->getData('points_total') + $pointAmount,
        ];
        $accountModel->addData($data)->save();

        $orderId = $creditmemo->getData('order_id');
        $loadTran = $this->_transactionFactory->create()
            ->getCollection()
            ->addFieldToFilter('order_id', $orderId)
            ->getFirstItem();

        if ($loadTran->getData()) {
            $expiredId = $loadTran->getData('expired_id');
            $arrExpired = explode(',', $expiredId);
            $expiredData = [];
            //Get the original point used
            foreach ($arrExpired as $data) {
                if ($data != '') {
                    $loadExpire = $this->_expiredFactory->create()->load($data);
                    $expiredData[] = $loadExpire->getData();
                }
            }
            if (is_array($expiredData)) {
                foreach ($expiredData as $userPoint) {
                    if ($pointAmount > 0) {
                        //Get tran id
                        $tranId = $userPoint['transaction_id'];
                        //getData tran
                        $transaction = $this->_transactionFactory->create()->load($tranId);
                        //Get the first point earned
                        $pointChange = $transaction->getData('points_change');
                        if ($pointChange > $transaction->getData('point_refund')) {
                            //
                            if ($pointAmount > $pointChange) {
                                $pointAmount -= $pointChange;
                                $this->update($creditmemo, $customerId, $pointChange, $account, $comment, $pointAmount, $userPoint);
                            } else {
                                $pointRefund = $transaction->getData('point_refund');
                                //Calculate the number of points used for the current refund and the refunded point for the previous refund
                                $totalPoint = $pointAmount + $pointRefund;
                                //In case the point used when refund is larger than the point used initially
                                if ($totalPoint > $pointChange) {
                                    //The remaining points are used for the current expired
                                    $scoreUsed = $pointChange - $pointRefund;
                                    //Update point refund to use for next time
                                    $transaction->setData('point_refund', $pointRefund + $scoreUsed)->save();
                                    $this->update($creditmemo, $customerId, $scoreUsed, $account, $comment, $pointAmount, $userPoint);
                                    //Calculate the number of points for the next transaction expired
                                    $pointsRemaining = $pointAmount - $scoreUsed;
                                    $pointAmount = $pointsRemaining;
                                } else {
                                    $pointChange = $pointAmount;
                                    //Update point refund to use for next time
                                    $transaction->setData('point_refund', $pointRefund + $pointChange)->save();
                                    $this->update($creditmemo, $customerId, $pointChange, $account, $comment, $pointAmount, $userPoint);
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function update($creditmemo, $customerId, $pointChange, $account, $comment, $pointAmount, $userPoint) {
        //Create a new transaction when refunding
        $createTran = $this->createTranRefund($creditmemo, $customerId, $pointChange, $account, $comment);
        $this->createExpiredReund($createTran, $pointAmount, $userPoint, $creditmemo, $customerId, $pointChange, $account);
    }


    /**
     * @param $creditmemo
     * @param $customerId
     * @param $pointChange
     * @param $account
     * @param $comment
     * @return mixed
     * @throws \Exception
     */
    public function createTranRefund($creditmemo, $customerId, $pointChange, $account, $comment) {
        $transactionModel = $this->_transactionFactory->create();
        $data = [
            'rule_id' => Rule::POINT_RETURN_BY_REFUND,
            'order_id' => $creditmemo->getOrder()->getId(),
            'customer_id' => $customerId,
            'points_change' => $pointChange,
            'points_after' => $account->getData('points_current') + $pointChange,
            'comment' => $comment
        ];
        $account->setData('points_current', $account->getData('points_current') + $pointChange);
        $transactionModel->addData($data)->save();
        $transactionId = $transactionModel->getId();
        return $transactionId;

    }

    /**
     * @param $transactionId
     * @param $pointAmount
     * @param $userPoint
     * @param $creditmemo
     * @param $customerId
     * @param $pointChange
     * @param $account
     * @throws \Exception
     */
    public function createExpiredReund($transactionId, $pointAmount, $userPoint, $creditmemo, $customerId, $pointChange, $account) {
        if ($pointAmount > 0) {
            $expiredModel = $this->_expiredFactory->create();
            $timeToExpired = $userPoint['expiry_type'];
            $expiredDate = $userPoint['expired_date'];

            $data = [
                'transaction_id' => $transactionId,
                'rule_id' => Rule::POINT_RETURN_BY_REFUND,
                'order_id' => $creditmemo->getOrder()->getId(),
                'customer_id' => $customerId,
                'points_change' => $pointChange,
                'expired_date' => $expiredDate,
                'status' => 'available',
                'expiry_type' => (bool)$timeToExpired
            ];
            //Send Email Refund
            $point = $pointAmount;
            $rule_id = Rule::POINT_RETURN_BY_REFUND;
            $this->sendEmailRefund($creditmemo, $point, $account, $rule_id);
            $expiredModel->addData($data)->save();
        }
    }


        /**
     * @param $creditmemo
     * @param $point
     * @param $account
     * @param $rule_id
     */
	public function sendEmailRefund($creditmemo, $point, $account, $rule_id) {
        if ($this->_hlp->getBalanceEmailEnable()) {
            $order = $creditmemo->getOrder();
            $this->_hlp->getSendEmail($order, $account, $point, $rule_id, null, null);
        }
    }
}
