<?php

namespace Magenest\AdminActivity\Model;

/**
 * Class LoginRepository
 *
 * @package Magenest\AdminActivity\Model
 */
class LoginRepository implements \Magenest\AdminActivity\Api\LoginRepositoryInterface
{
	/**
	 * @var boolean
	 */
	const LOGIN_SUCCESS = 1;
	/**
	 * @var boolean
	 */
	const LOGIN_FAILED = 0;

	/**
	 * @var LoginFactory
	 */
	public $loginFactory;

	/**
	 * @var Processor
	 */
	public $processor;

	/**
	 * @var object Magento\User\Model\User
	 */
	public $user;

	/**
	 * @var ResourceModel\Activity\CollectionFactory
	 */
	public $collectionFactory;

	/**
	 * LoginRepository constructor.
	 *
	 * @param LoginFactory $loginFactory
	 * @param ResourceModel\Login\CollectionFactory $collectionFactory
	 * @param Processor $processor
	 */
	public function __construct(
		\Magenest\AdminActivity\Model\LoginFactory $loginFactory,
		\Magenest\AdminActivity\Model\ResourceModel\Login\CollectionFactory $collectionFactory,
		\Magenest\AdminActivity\Model\Processor $processor
	) {
		$this->loginFactory      = $loginFactory;
		$this->collectionFactory = $collectionFactory;
		$this->processor         = $processor;
	}

	/**
	 * Get login user
	 *
	 * @return string
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * Set login user
	 *
	 * @param $user
	 * @return $this
	 */
	public function setUser($user)
	{
		$this->user = $user;
		return $this;
	}

	/**
	 * Set login activity data
	 *
	 * @return mixed
	 */
	public function _initLoginActivity()
	{
		$login = $this->loginFactory->create();

		$user = $this->getUser();
		if ($user != null && $user instanceof \Magento\User\Model\User) {
			$login->setUsername($user->getUsername());
			$login->setName(ucwords($user->getName()));
		}

		$login->setRemoteIp($this->processor->remoteAddress->getRemoteAddress());
		$login->setForwardedIp($this->processor->request->getServer('HTTP_X_FORWARDED_FOR'));
		$login->setUserAgent($this->processor->handler->header->getHttpUserAgent());

		return $login;
	}

	/**
	 * Set login data
	 *
	 * @param $status
	 * @param $type
	 * @param string $remark
	 * @return boolean
	 */
	public function addLog($status, $type, $remark = '')
	{
		$login = $this->_initLoginActivity();

		$login->setStatus($status);
		$login->setType($type);
		$login->setRemarks($remark);
		$login->save();

		return true;
	}

	/**
	 * Track login success log
	 *
	 * @return void
	 */
	public function addSuccessLog()
	{
		$this->addLog(self::LOGIN_SUCCESS, 'Login');
	}

	/**
	 * Track login fail log
	 *
	 * @param string $remark
	 */
	public function addFailedLog($remark = '')
	{
		$this->addLog(self::LOGIN_FAILED, 'Login', $remark);
	}

	/**
	 * Track logout success log
	 *
	 * @return void
	 */
	public function addLogoutLog()
	{
		$this->addLog(self::LOGIN_SUCCESS, 'Logout');
	}

	/**
	 * Get all admin activity data before date
	 *
	 * @param $endDate
	 * @return $this
	 */
	public function getListBeforeDate($endDate)
	{
		$collection = $this->collectionFactory->create()
			->addFieldToSelect('entity_id')
			->addFieldToFilter('created_at', ["lteq" => $endDate]);

		return $collection;
	}
}
