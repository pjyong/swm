<?php
return array(
	'db' => array(
		'db'=>array(
			'host' => '119.23.129.226',
			'user' => 'dbadmin',
			'password' => 'y86tTqbB830P',
			'driver' => 'pdo_mysql',
			'port' => 7755,
			'dbname' => 'cheyoo_system',
		),

	    //主库 119.23.129.226
		'db1'=>array(
			'host' => '119.23.129.226',
			'user' => 'dbadmin',
			'password' => 'y86tTqbB830P',
			'driver' => 'pdo_mysql',
			'port' => 7755,
			'dbname' => 'cheyoo',
		),
	),

	'ssdb'=>array(
		'host'=>'192.168.31.234',
		'port'=>8888,
		'timeout'=>30
	),
);
