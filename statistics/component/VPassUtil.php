<?php
/**
 * Created by PhpStorm.
 * User: cc
 * Date: 16/9/8
 * Time: 下午5:00
 */
namespace statistics\component;

use sdk\MMThrift;

require_once 'vpass/MMThrift.php';


class VPassUtil
{
    public static function getVPassClient()
    {
        return MMThrift::getVpassClient();
    }
}

