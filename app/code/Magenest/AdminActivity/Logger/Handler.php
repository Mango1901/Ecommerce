<?php

namespace Magenest\AdminActivity\Logger;

/**
 * Class Handler
 *
 * @package Magenest\AdminActivity\Logger
 */
class Handler extends \Magento\Framework\Logger\Handler\Base
{
	/**
	 * Logging level
	 *
	 * @var int
	 */
	public $loggerType = \Magenest\AdminActivity\Logger\Logger::INFO;

	/**
	 * File name
	 *
	 * @var string
	 */
	public $fileName = '/var/log/adminactivity.log';
}
