<?php
namespace SyncIt\ApiRestLog\Model\Logger;

use Magento\Framework\Logger\Handler\Base;
use \Monolog\Logger;

/**
 * Class Handler
 * @package SyncIt\ApiRestLog\Model\Logger
 */
class Handler extends Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::DEBUG;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/syncit_rest_api.log';
}
