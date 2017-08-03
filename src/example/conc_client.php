<?php
// $data = array(0=>1,1=>2,2=>3);
// $msg = msgpack_pack($data);
// $data = msgpack_unpack($msg);
// print_r($data);
// exit;
function callback($retval, $callinfo) {
    print 'running before:';
    print_r($callinfo);
    print $retval;
    print "<br/>";
}

function error_callback($type, $error, $callinfo) {
    print_r(json_encode($error));
}

$api_url = "http://api.cheyoo.com/sms/";
Yar_Concurrent_Client::call($api_url , "sendMsgByConcurrent", array("firstname1", "lastname1"), "callback", "error_callback");
Yar_Concurrent_Client::call($api_url , "sendMsgByConcurrent", array("firstname2", "lastname2"), "callback", "error_callback");
Yar_Concurrent_Client::call($api_url , "sendMsgByConcurrent", array("firstname3", "lastname3"), "callback", "error_callback");
/*
Yar_Concurrent_Client::call($api_url, "some_method", array("parameters"));   // if the callback is not specificed,
// callback in loop will be used
Yar_Concurrent_Client::call($api_url, "some_method", array("parameters"), "callback", "error_callback", array(YAR_OPT_PACKAGER => "json"));
//this server accept json packager
Yar_Concurrent_Client::call($api_url, "some_method", array("parameters"), "callback", "error_callback", array(YAR_OPT_TIMEOUT=>1));
//custom timeout

//the error_callback is optional
*/
Yar_Concurrent_Client::loop("callback", "error_callback"); //send the requests,

?>
