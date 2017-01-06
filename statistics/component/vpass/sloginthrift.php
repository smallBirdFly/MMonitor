<?php
/**
 * Created by PhpStorm.
 * User: linsainan
 * Date: 16/8/31
 * Time: 下午1:52
 */
use sdk\MMThrift;
use vpasscon\vpassClient;

session_start();
header('Content-Type:text/javascript; charset=utf-8');
$mmcode = $_GET['mmcode'];
if(!empty($mmcode)){
    MMThrift::setupVpassClient();
    $client = vpassClient::instance('VPass');
    $response = $client->getToken($mmcode);
    $response = json_decode($response,true);
    if(!empty($response)&&$response['code']==200)
    {
        $token = $response['data']['x-access-token'];
        $userInfo = $response['data']['userInfo'];

        if(!empty($token))
        {
            header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
            $_SESSION['token'] = $token;
            if(!empty($userInfo['userName']))
            {
                $_SESSION['username'] = $userInfo['userName'];
            }
            if(!empty($userInfo['mobile']))
            {
                $_SESSION['mobile'] = $userInfo['mobile'];
            }

            $_SESSION['u_id'] = $userInfo['id'];
            $_SESSION['expires_in'] = $userInfo['expireTime'];
        }
    }
}