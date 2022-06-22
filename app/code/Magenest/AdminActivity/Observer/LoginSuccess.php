<?php

namespace Magenest\AdminActivity\Observer;

use Magenest\AdminActivity\Helper\Data as Helper;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class LoginSuccess
 *
 * @package Magenest\AdminActivity\Observer
 */
class LoginSuccess implements ObserverInterface
{
	/**
	 * @var Helper
	 */
	public $helper;

	/**
	 * @var \Magenest\AdminActivity\Api\LoginRepositoryInterface
	 */
	public $loginRepository;

	/**
	 * @var \Magenest\AdminActivity\Helper\Benchmark
	 */
	public $benchmark;

	/**
	 * LoginSuccess constructor.
	 *
	 * @param Helper $helper
	 * @param \Magenest\AdminActivity\Api\LoginRepositoryInterface $loginRepository
	 * @param \Magenest\AdminActivity\Helper\Benchmark $benchmark
	 */
	public function __construct(
		Helper $helper,
		\Magenest\AdminActivity\Api\LoginRepositoryInterface $loginRepository,
		\Magenest\AdminActivity\Helper\Benchmark $benchmark
	) {
		$this->helper          = $helper;
		$this->loginRepository = $loginRepository;
		$this->benchmark       = $benchmark;
	}

	/**
	 * Login success
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

		$this->loginRepository
			->setUser($observer->getUser())
			->addSuccessLog();
		$this->benchmark->end(__METHOD__);
	}
}
