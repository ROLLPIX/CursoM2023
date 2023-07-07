<?php
namespace Modo\Gateway\Logger\Handler;

use Magento\Framework\Logger\Handler\Base as BaseHandler;
use Monolog\Logger as MonologLogger;

class InfoHandler extends BaseHandler
{
    protected $loggerType = MonologLogger::INFO;

    protected $fileName = 'var/log/modo_payment/info.log';
}
