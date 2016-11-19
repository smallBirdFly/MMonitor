<?php
return [
    'db' => [
        'dsn' => 'mysql:host=127.0.0.1;dbname=mmonitor',
        'username' => 'root',
        'password' => '123456',
    ],
    "env" => "dev",       //local, dev or prod
    "debug" => true,
    "host" => '127.0.0.1',
    "port" => 6379,
    "database" => 'mmonitor'
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
