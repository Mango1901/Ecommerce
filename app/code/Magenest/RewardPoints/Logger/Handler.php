<?php

namespace Magenest\RewardPoints\Logger;

use Magento\Framework\Logger\Handler\Base;

/**
 * Class Handler
 * @package Magenest\RewardPoints\Logger
 */
class Handler extends Base
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/rewardpoints/debug.log';

    /**
     * @var int
     */
    protected $loggerType = \Monolog\Logger::DEBUG;
}