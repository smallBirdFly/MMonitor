<?php
date_default_timezone_set("Asia/Shanghai");
ini_set('date.timezone','Asia/Shanghai');

$mmconf = require(__DIR__ . '/../../mmconf/dist/mm.php');

defined('YII_DEBUG') or define('YII_DEBUG', $mmconf['debug']);
defined('YII_ENV') or define('YII_ENV', $mmconf['env']);

// defined ('YII_DEBUG') or define ('YII_DEBUG', false );
// defined ('YII_ENV') or define ('YII_ENV', 'PRODUCTION');

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/bootstrap.php');
require(__DIR__ . '/../config/bootstrap.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../common/config/main.php'),
    require(__DIR__ . '/../../common/config/main-local.php'),
    require(__DIR__ . '/../config/main.php'),
    require(__DIR__ . '/../config/main-local.php')
);

$application = new yii\web\Application($config);
$application->run();
