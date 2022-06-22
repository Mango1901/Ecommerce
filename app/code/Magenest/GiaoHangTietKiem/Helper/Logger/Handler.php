<?php

namespace Magenest\GiaoHangTietKiem\Helper\Logger;

use Magento\Framework\Logger\Handler\Base;

/**
 * Class Handler
 * @package Magenest\GiaoHangTietKiem\Helper\Logger
 */
class Handler extends Base
{
	/**
	 * @var string
	 */
	protected $fileName = '/var/log/shipment/ghtk.log';

	/**
	 * @var int
	 */
	protected $loggerType = \Monolog\Logger::DEBUG;
}
