<?php
$mmconf = require(__DIR__ . '/../../mmconf/dist/mm.php');
$db = $mmconf['db'];

\common\components\MMLogger::setup();

return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],

        'session' => [
            'class' => 'yii\web\DbSession',
            'timeout' => 36000
        ],

        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => $db['dsn'],
            'username' => $db['username'],
            'password' => $db['password'],
            'charset' => 'utf8',
        ],
    ]
];
