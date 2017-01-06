<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/11
 * Time: 15:01
 */
namespace common\utils;

use statistics\component\VpassUrl;

class SessionUtil
{
    //判定当前的登录状态
    public static function isLogin()
    {
        $session = \Yii::$app->session;
        \Yii::error($session['u_id']);
        if(isset($session['u_id']))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    //获取登录url
    public static function getLoginUrl()
    {
        return VpassUrl::getLoginUrl();
    }
}