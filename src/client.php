<?php
require __DIR__ . '/../vendor/autoload.php';

$client = new Yar_Client( ga('Sms') );
$client->SetOpt(YAR_OPT_CONNECT_TIMEOUT, 1000);
$client->SetOpt(YAR_OPT_PACKAGER, "json");

/* call remote service */
$result = $client->sendMsg("firstname", "lastname");
print $result;

function ga( $m )
{
    return 'http://api.cheyoo.com/src/?m=' . $m;
}
