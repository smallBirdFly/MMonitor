<?php
/**
 * Created by PhpStorm.
 * User: cc
 * Date: 16/11/2
 * Time: 上午10:32
 */

$client = new SoapClient("http://60.173.235.154:838/TrainService.asmx?wsdl", array(
        'trace' => true,
        'exceptions' => true,
        'encoding'=>'UTF-8',
        'cache_wsdl' => WSDL_CACHE_NONE,
        'soap_version' => SOAP_1_2)
);
print_r('1');
print_r($client->__getFunctions());
print_r('2');
print_r($client->__getTypes());

try
{
    $verify_reply = $client->__soapCall("Verify", [
        "token" => "B464MLJ7wzs%3d",
        "operateId" => "22669"
    ]);
    var_dump($verify_reply);

    $verify_reply1 = $client->__soapCall("ExpertSynchronous", [
        "operateId" => "22669"
    ]);
    var_dump($verify_reply1);
}
catch(Exception $e)
{
    print_r('3');
    print_r($e);
}

