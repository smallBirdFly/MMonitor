<?php
/**
 * Created by PhpStorm.
 * User: sainan
 * Date: 2016/6/14
 * Time: 14:33
 */
namespace common\utils;

use Yii;
use yii\web\Cookie;
use yii\web\Response;
class HttpResponseUtil
{
    public static function setJsonResponse($result)
    {
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response->data = $result;
    }

    public static function setResponseCookie($name,$value,$expireTime=null)
    {
        $headers = Yii::$app->response->headers;
        $headers->add("P3P",'CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');

        $cookies = Yii::$app->response->cookies;
        $cookie = new Cookie([
            'name' => $name,
            'value' => $value,
        ]);
        if(null != $expireTime)
        {
            $cookie->expire = time() + $expireTime;
        }
        $cookie->path = '/survey/';
        $cookies->add($cookie);
    }

    public static function clearCookie()
    {
        $cookies = Yii::$app->response->cookies;
        $cookies->remove('pass');
    }
}