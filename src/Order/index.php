<?php
require __DIR__ . '/../../vendor/autoload.php';

use Cheyoo\Order\Api;

//
$service = new Yar_Server(new Api());
$service->handle();
