<?php
/**
 * Created by PhpStorm.
 * User: linsainan
 * Date: 16/6/23
 * Time: 上午9:17
 */
namespace common\utils;

class UrlUtil
{
    /**
     * 组装url和地址参数
     * @param $baseURL 请求的url
     * @param $keysArr 请求参数
     * @return string
     */
    public function combineURL($baseURL,$keysArr){
        $combined = $baseURL."?";
        $valueArr = array();

        foreach($keysArr as $key => $val){
            $valueArr[] = "$key=$val";
        }

        $keyStr = implode('&',$valueArr);
        $combined .= ($keyStr);

        return $combined;
    }

    /**
     * get请求
     * @param $url
     * @return mixed|string
     */
    public function get_contents($url){
        if (ini_get("allow_url_fopen") == "1") {
            $response = file_get_contents($url);
        }else{
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_URL, $url);
            $response =  curl_exec($ch);
            curl_close($ch);
        }

        return $response;
    }

    /**
     * get请求
     * @param $url
     * @param $keysArr
     * @return mixed|string
     */
    public function get($url, $keysArr){
        $combined = $this->combineURL($url, $keysArr);
        return $this->get_contents($combined);
    }

    /**
     * get with header
     * @param $url
     * @param $headers
     * @param null $keysArr
     * @return mixed
     */
    public function getWithHeader($url,$headers,$keysArr=null)
    {
        if($keysArr != null)
        {
            $url = $this->combineURL($url,$keysArr);
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * post请求
     * @param $url
     * @param $keysArr
     * @param int $flag
     * @return mixed
     */
    public function post($url, $keysArr, $flag = 0){
        $ch = curl_init();
        if(! $flag)
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $keysArr);
        curl_setopt($ch, CURLOPT_URL, $url);
        $ret = curl_exec($ch);

        curl_close($ch);
        return $ret;
    }

    public function postWithHeader($url, $keysArr,$headers, $flag = 0){
        $ch = curl_init();
        if(! $flag)
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $keysArr);
        if(!empty($headers))
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        $ret = curl_exec($ch);

        curl_close($ch);
        return $ret;
    }
}