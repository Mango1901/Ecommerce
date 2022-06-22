<?php

namespace Magenest\AdminActivity\Observer;

use Magenest\AdminActivity\Helper\Data as Helper;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class LoginFailed
 *
 * @package Magenest\AdminActivity\Observer
 */
class LoginFailed implements ObserverInterface
{
	/**
	 * @var Helper
	 */
	public $helper;

	/**
	 * @var \Magento\User\Model\User
	 */
	public $user;

	/**
	 * @var \Magenest\AdminActivity\Api\LoginRepositoryInterface
	 */
	public $loginRepository;

	/**
	 * @var \Magenest\AdminActivity\Helper\Benchmark
	 */
	public $benchmark;

	/**
	 * LoginFailed constructor.
	 *
	 * @param Helper $helper
	 * @param \Magento\User\Model\User $user
	 * @param \Magenest\AdminActivity\Api\LoginRepositoryInterface $loginRepository
	 * @param \Magenest\AdminActivity\Helper\Benchmark $benchmark
	 */
	public function __construct(
		Helper $helper,
		\Magento\User\Model\User $user,
		\Magenest\AdminActivity\Api\LoginRepositoryInterface $loginRepository,
		\Magenest\AdminActivity\Helper\Benchmark $benchmark
	) {
		$this->helper          = $helper;
		$this->user            = $user;
		$this->loginRepository = $loginRepository;
		$this->benchmark       = $benchmark;
	}

	/**
	 * Login failed
	 *
	 * @param \Magento\Framework\Event\Observer $observer
	 * @return void
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$this->benchmark->start(__METHOD__);
		if (!$this->helper->isLoginEnable()) {
			return $observer;
		}

		$user = null;
		if ($observer->getUserName()) {
			$user = $this->user->loadByUsername($observer->getUserName());
		}

		$this->loginRepository
			->setUser($user)
			->addFailedLog($observer->getException()->getMessage());
		$this->benchmark->end(__METHOD__);
	}
}
