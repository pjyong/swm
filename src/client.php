<?php
// 客户端模拟
// $client = new Yar_Client( ga('Sms') );
// $client->SetOpt(YAR_OPT_CONNECT_TIMEOUT, 1000);
// $client->SetOpt(YAR_OPT_PACKAGER, "json");
// $result = $client->sendMsg("firstname", "lastname");
// print $result;

// 查找某个人的订单
$client = new Yar_Client( ga('Order') );
$client->SetOpt(YAR_OPT_CONNECT_TIMEOUT, 1000);
$client->SetOpt(YAR_OPT_PACKAGER, "json");
$result = $client->getOrdersByUID( 61200 );
print_r( $result );



// 并行调用
/*
function callback($retval, $callinfo) {
    print 'running before:';
    print_r($callinfo);
    print $retval;
    print "<br/>";
}

function error_callback($type, $error, $callinfo) {
    print_r(json_encode($error));
}

Yar_Concurrent_Client::call(ga('Sms') , "sendMsgByConcurrent", array("firstname1", "lastname1"), "callback", "error_callback");
Yar_Concurrent_Client::call(ga('Sms') , "sendMsgByConcurrent", array("firstname2", "lastname2"), "callback", "error_callback");
Yar_Concurrent_Client::call(ga('Sms') , "sendMsgByConcurrent", array("firstname3", "lastname3"), "callback", "error_callback");
Yar_Concurrent_Client::loop("callback", "error_callback"); //send the requests,
*/
function ga( $m )
{
    return 'http://api.cheyoo.com/src/api_index.php?m=' . $m;
}
