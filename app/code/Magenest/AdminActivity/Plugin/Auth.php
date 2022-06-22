<?php

namespace Magenest\AdminActivity\Plugin;

use Magenest\AdminActivity\Helper\Data as Helper;

/**
 * Class Auth
 *
 * @package Magenest\AdminActivity\Plugin
 */
class Auth
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
	 * Auth constructor.
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
	 * Track admin logout activity
	 *
	 * @param \Magento\Backend\Model\Auth $auth
	 * @param callable $proceed
	 * @return mixed
	 */
	public function aroundLogout(\Magento\Backend\Model\Auth $auth, callable $proceed)
	{
		$this->benchmark->start(__METHOD__);
		if ($this->helper->isLoginEnable()) {
			$user = $auth->getAuthStorage()->getUser();
			$this->loginRepository->setUser($user)->addLogoutLog();
		}
		$result = $proceed();
		$this->benchmark->end(__METHOD__);
		return $result;
	}
}
