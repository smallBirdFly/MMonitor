<?php
namespace common\components;
require(__DIR__ . '/../lib/apache-log4php-2.3.0/src/main/php/Logger.php');

class MMLogger
{
    const VERSION = 'v1.0.0';

    public static function setup()
    {
        \Logger::configure(__DIR__ . '/../config/log.config.xml');
    }
    
    public static function getLogger($tag)
    {
        return \Logger::getLogger($tag);
    }
}
