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

namespace Magenest\RewardPoints\Plugin;


class RefundPointCheck
{
	/**
	 * @var \Magento\Framework\App\RequestInterface
	 */
	protected $_request;

	public function __construct(\Magento\Framework\App\RequestInterface $request)
	{
		$this->_request = $request;
	}

	/**
	 * @param \Magento\Sales\Model\Service\CreditmemoService $subject
	 * @param \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo
	 * @param bool $offlineRequested
	 * @return array
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function beforeRefund(
		\Magento\Sales\Model\Service\CreditmemoService $subject,
		\Magento\Sales\Api\Data\CreditmemoInterface $creditmemo,
		$offlineRequested = false
	) {
		$data = $this->_request->getPost('creditmemo');
		if (isset($data['do_offline'])) {
			//do not allow online refund for Refund as Reward Points
			if (!$data['do_offline'] && !empty($data['refund_mgn_reward_points_return_enable'])) {
				throw new \Magento\Framework\Exception\LocalizedException(
					__('Cannot create online refund for Refund to Store Credit.')
				);
			}
		}

		return [$creditmemo, $offlineRequested];
	}
}