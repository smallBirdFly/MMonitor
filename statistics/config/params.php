<?php
return [
    'adminEmail' => 'admin@example.com',
    //每次从redis往数据库存储的数据条数
    'num' => 5,
    "vpass" => [
        "url" => "http://192.168.1.155",
        "login" => "/vpass/site/login",
        "register" => "/vpass/site/register",
        "get_token" => '/vpass/api/site/get-token',
        "logout"=> '/vpass/site/logout',
        "thrift"=> '192.168.1.155:9094',
        "appkey" => 398500135,
    ]
];
