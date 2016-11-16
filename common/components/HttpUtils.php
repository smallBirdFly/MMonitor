<?php
namespace common\components;


class HttpUtils
{
    /**
     * 发送请求
     * 使用POST方式, 连接超时5s, 执行等待超时10s
     * @param $url
     * @param null $data
     * @return array
     */
    public static function http_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);

        if(!empty($data))
        {
            $data_str = json_encode($data);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_str);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json; charset=utf-8',
                    'Content-Length: ' . strlen($data_str))
            );
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        $err = curl_errno($curl);
        curl_close($curl);
        return ["errno" => $err, "msg" => $output];
    }
}