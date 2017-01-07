<?php
return [
    'db' => [
        'dsn' => 'mysql:host=127.0.0.1;dbname=mmonitor',
        'username' => 'root',
        'password' => '123456',
    ],
    "env" => "dev",       //local, dev or prod
    "debug" => true,
    'redis' => [
        'class' => 'yii\redis\Connection',
//        "host" => '139.129.94.52',
        "host" => '127.0.0.1',
        "port" => 6379,
        "database" => 0
    ]
];

//return [
//    'db' => [
//        'dsn' => 'mysql:host=127.0.0.1;dbname=test',
//        'username' => 'root',
//        'password' => '123456',
//    ],
//    "env" => "prod",       //local, dev or prod
//    "debug" => false,
//];
