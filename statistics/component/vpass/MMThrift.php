<?php
/**
 * Created by PhpStorm.
 * User: linsainan
 * Date: 16/9/8
 * Time: 下午7:20
 */

namespace sdk;

use vpasscon\vpassClient;
use Yii;

require_once __DIR__.'/vpasscon/vpassClient.php';
require_once __DIR__.'/vpasscon/vpass/Types.php';

class MMThrift
{
    public static $isSetup = 0;
    public static function setupVpassClient(){
        // 传入配置，一般在某统一入口文件中调用一次该配置接口即可
        vpassClient::config(array(
                'vpass' => array(
                    'addresses' => array(
                        Yii::$app->params['vpass']['thrift'],
                    ),
                    'thrift_protocol' => 'TBinaryProtocol',//不配置默认是TBinaryProtocol，对应服务端HelloWorld.conf配置中的thrift_protocol
                    'thrift_transport' => 'TFramedTransport',//不配置默认是TBufferedTransport，对应服务端HelloWorld.conf配置中的thrift_transport
                ),
            )
        );
    }

    public static function getVpassClient()
    {
        if(MMThrift::$isSetup == 0)
        {
            MMThrift::setupVpassClient();
            MMThrift::$isSetup = 1;
        }

        $client = vpassClient::instance('vpass');

        return $client;
    }
}