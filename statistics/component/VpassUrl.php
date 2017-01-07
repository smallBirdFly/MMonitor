<?php
/**
 * Created by PhpStorm.
 * User: linsainan
 * Date: 16/9/9
 * Time: 下午1:50
 */

namespace statistics\component;


use common\utils\HttpResponseUtil;
use Yii;
use yii\helpers\Url;

class VpassUrl
{
    public function behaviors()
    {
        return [
            'auth' => [
                'class' => AuthFilter::className(),
                'except' => ['login'],
            ]
        ];
    }
    public static function getLoginUrl()
    {
        $host = Yii::$app->getRequest()->headers->get("host");
        $action = Yii::$app->requestedAction;
        $route = Url::to([$action->controller->module->requestedRoute]);
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        $url = $http_type.$host.$route;
        if(!empty($_SERVER['QUERY_STRING'])){
            $url = $url.'?'.$_SERVER['QUERY_STRING'];
        }
        $vpass = Yii::$app->params['vpass'];
//        Yii::error($vpass['url'].$vpass['login']."?callback_uri=".urlencode($url).'&appkey='.$vpass['appkey']);
        return $vpass['url'].$vpass['login']."?callback_uri=".urlencode($url).'&appkey='.$vpass['appkey'];
    }

    public static function getRegisterUrl()
    {
        $host = Yii::$app->getRequest()->headers->get("host");
        $action = Yii::$app->requestedAction;
        $route = Url::to([$action->controller->module->requestedRoute]);
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        $url = $http_type.$host.$route;
        if(!empty($_SERVER['QUERY_STRING'])){
            $url = $url.'?'.$_SERVER['QUERY_STRING'];
        }
        $vpass = Yii::$app->params['vpass'];
        return $vpass['url'].$vpass['register']."?callback_uri=".urlencode($url);
    }

    public static function getTokenCreateUrl()
    {
        $vpass = Yii::$app->params['vpass'];
        return $vpass['url'].$vpass['get_token'];
    }

    public static function getLogoutUrl()
    {
        $vpass = Yii::$app->params['vpass'];
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        $host = Yii::$app->getRequest()->headers->get("host");
        $route = Url::to(['site/index']);
        $url = $http_type.$host.$route;
        if(!empty($_SERVER['QUERY_STRING'])){
            $url = $url.'?'.$_SERVER['QUERY_STRING'];
        }

        return $vpass['url'].$vpass['logout']."?callback_uri=".urlencode($url).'&appkey='.$vpass['appkey'];
    }

}