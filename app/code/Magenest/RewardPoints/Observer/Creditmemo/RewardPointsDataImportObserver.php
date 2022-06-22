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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\Order\Creditmemo;

class RewardPointsDataImportObserver implements ObserverInterface
{
	/**
	 * @var PriceCurrencyInterface
	 */
	protected $priceCurrency;

	/**
	 * @var ScopeConfigInterface
	 */
	private $scopeConfig;

	/**
	 * CreditmemoDataImportObserver constructor.
	 *
	 * @param PriceCurrencyInterface $priceCurrency
	 * @param ScopeConfigInterface $scopeConfig
	 */
	public function __construct(
		PriceCurrencyInterface $priceCurrency,
		ScopeConfigInterface $scopeConfig
	) {
		$this->priceCurrency = $priceCurrency;
		$this->scopeConfig = $scopeConfig;
	}

	/**
	 * Sets refund flag for manual refund (with "Refund as Reward Points" input)
	 * or for return | deduct reward points to customer if customer use | earn points for this order
	 * used for event: adminhtml_sales_order_creditmemo_register_before
	 *
	 * @param Observer $observer
	 * @return void
	 */
	public function execute(Observer $observer)
	{
		$creditmemo = $observer->getEvent()->getCreditmemo();
		$input = $observer->getEvent()->getInput();
		$refundMgnRewardPointsReturnEnable = !empty($input['refund_mgn_reward_points_return_enable']);
		$refundMgnRewardPointsAmount = !empty($input['refund_mgn_reward_points_return'])
			? $input['refund_mgn_reward_points_return']
			: null;

		if ($refundMgnRewardPointsReturnEnable && is_numeric($refundMgnRewardPointsAmount)) {
			$refundMgnRewardPointsAmount = max(
				0,
				min($creditmemo->getBaseGrandTotal(), $refundMgnRewardPointsAmount)
			);
			$this->prepareMgnRewardPointsForRefund($creditmemo, $refundMgnRewardPointsAmount);
		}

		$refundMgnReturnEnable = !empty($input['refund_mgn_reward_returns_return_enable']);
		$refundMgnReturnAmount = !empty($input['refund_mgn_reward_returns_return'])
			? (int)$input['refund_mgn_reward_returns_return']
			: null;
		if ($refundMgnReturnEnable && is_numeric($refundMgnReturnAmount)) {
			$this->prepareReturnRewardpointAmout($creditmemo, $refundMgnReturnAmount);
		}
	}

	/**
	 * Sets refund flag to creditmemo based on user input.
	 *
	 * @param Creditmemo $creditmemo
	 * @param float $amount
	 * @return void
	 */
	private function prepareMgnRewardPointsForRefund(Creditmemo $creditmemo, $amount)
	{
		if (!$this->validateAmount($amount)) {
			return;
		}

		$amount = $this->priceCurrency->round($amount);
		$creditmemo->setBsCustomerMgnRwpTotalRefunded($amount);

		$amount = $this->priceCurrency->round(
			$amount * $creditmemo->getOrder()->getBaseToOrderRate()
		);
		$creditmemo->setCustomerMgnRwpTotalRefunded($amount);
		//setting flag to make actual refund to customer balance after creditmemo save
		$creditmemo->setCustomerRewardPointsRefundFlag(true);
		//allow online refund
		$creditmemo->setPaymentRefundDisallowed(false);
	}

	/**
	 * Validates amount for refund.
	 *
	 * @param float $amount
	 * @return bool
	 */
	private function validateAmount($amount)
	{
		return is_numeric($amount) && ($amount > 0);
	}

	/**
	 * Set return point flag and amount for handle after save credit memo
	 *
	 * @param $creditmemo
	 * @param $refundMgnReturnAmount
	 */
	private function prepareReturnRewardpointAmout($creditmemo, $refundMgnReturnAmount)
	{
		if (!$this->validateAmount($refundMgnReturnAmount)) {
			return;
		}

		$creditmemo->setReturnRewardPointToCustomerFlag(true);
		$creditmemo->setReturnRewardPointToCustomerAmount($refundMgnReturnAmount);
	}
}
