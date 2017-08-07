<?php
define( 'ROOT_DIR', __DIR__ . '/' );
define( 'VENDOR_DIR', __DIR__ . '/../vendor/' );
require_once VENDOR_DIR . 'autoload.php';
use Cheyoo\System\Component\App;
$cy = new App( array(
    'settings' => array(
        'env' => 'dev'
    )
) );
$cy->start();
