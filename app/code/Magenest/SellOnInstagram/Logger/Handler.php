<?php
namespace Magenest\SellOnInstagram\Logger;

use Magento\Framework\Logger\Handler\Base;

class Handler extends Base
{
    protected $fileName = '/var/log/magenest/instagram_shop.log';
    protected $loggerType = \Monolog\Logger::DEBUG;
}
