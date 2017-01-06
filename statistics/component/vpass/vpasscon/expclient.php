<?php

    // 引入客户端文件
use vpasscon\vpassClient;

require_once './vpassClient.php';
require_once './vpass/Types.php';

// 传入配置，一般在某统一入口文件中调用一次该配置接口即可
vpassClient::config(array(
                         'VPass' => array(
                           'addresses' => array(
                               '127.0.0.1:9094',
                               //'127.0.0.2:9191',
                             ),
                             'thrift_protocol' => 'TBinaryProtocol',//不配置默认是TBinaryProtocol，对应服务端HelloWorld.conf配置中的thrift_protocol
                             'thrift_transport' => 'TFramedTransport',//不配置默认是TBufferedTransport，对应服务端HelloWorld.conf配置中的thrift_transport
                           ),
                           //'UserInfo' => array(
                             //'addresses' => array(
                               //'127.0.0.1:9393'
                             //),
                           //),
                         )
                       );
    // =========  以上在WEB入口文件中调用一次即可  ===========


    // =========  以下是开发过程中的调用示例  ==========

    // 初始化一个HelloWorld的实例
    $client = vpassClient::instance('VPass');
    // --------同步调用实例----------
    //oauthCode 换取token
    $token = $client->getToken('195445b9377b1b0649323a110bb3a15c');
    var_export($token);
    //解析token
    $tokenInfo = $client->verify('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjEiLCJtb2JpbGUiOiIxODgyMzg4NzIzNCIsInVzZXJOYW1lIjoic2FpbmFuIiwic2V4IjoiXHU3NTM3IiwicHJvdmluY2UiOiIiLCJjaXR5IjoiIiwiY291bnRyeSI6IkNOIiwiaGVhZF91cmwiOiIiLCJleHBpcmVUaW1lIjoxNDcyNjE2MjA2fQ.TJPc1Vv9tt3ye_lm8qJH30KSh33VWm-qPFYV9trLwDs');
    var_dump($tokenInfo);
    $logout = $client->logout('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjEiLCJtb2JpbGUiOiIxODgyMzg4NzIzNCIsInVzZXJOYW1lIjoic2FpbmFuIiwic2V4IjoiXHU3NTM3IiwicHJvdmluY2UiOiIiLCJjaXR5IjoiIiwiY291bnRyeSI6IkNOIiwiaGVhZF91cmwiOiIiLCJleHBpcmVUaW1lIjoxNDcyNjE2MjA2fQ.TJPc1Vv9tt3ye_lm8qJH30KSh33VWm-qPFYV9trLwDs');
    var_dump($logout);



