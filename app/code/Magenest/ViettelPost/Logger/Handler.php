<?php
/**
 * Created by Magenest JSC.
 * Author: Jacob
 * Date: 31/01/2019
 * Time: 9:41
 */

namespace Magenest\ViettelPost\Logger;

use Magento\Framework\Logger\Handler\Base;

class Handler extends Base
{
    protected $fileName = '/var/log/viettelpost/debug.log';
    protected $loggerType = \Monolog\Logger::DEBUG;
}
