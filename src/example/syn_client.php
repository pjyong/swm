<?php
$client = new Yar_Client("http://api.cheyoo.com/sms/");
/* the following setopt is optinal */
//Set timeout to 1s
$client->SetOpt(YAR_OPT_CONNECT_TIMEOUT, 1000);

//Set packager to JSON
$client->SetOpt(YAR_OPT_PACKAGER, "json");

/* call remote service */
$result = $client->sendMsg("firstname", "lastname");
echo "<br>";
var_dump($result);
?>
