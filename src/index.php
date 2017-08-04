<?php
define( 'VENDOR_DIR', __DIR__ . '/../vendor/' );
require_once VENDOR_DIR . 'autoload.php';
// 目前全局变量只有一个$cy
$cy = \Cheyoo\System\Component\Cy::instance();
$cy->start();
