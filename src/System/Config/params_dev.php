<?php
return array(
	'db'=>array(
		'connectionString' => 'mysql:host=119.23.129.226;port=7755;dbname=cheyoo_system',
		'emulatePrepare' => true,
		'username' => 'dbadmin',
		'password' => 'y86tTqbB830P',
		'charset' => 'utf8',
		'class' => 'CDbConnection'
	),

    //主库 119.23.129.226
	'db1'=>array(
		'connectionString' => 'mysql:host=119.23.129.226;port=7755;dbname=cheyoo',
		'emulatePrepare' => true,
		'username' => 'dbadmin',
		'password' => 'y86tTqbB830P',
		'charset' => 'utf8',
		'class' => 'CDbConnection'
	),

    //从库 120.76.30.211
	'db2'=>array(
		'connectionString' => 'mysql:host=120.76.30.211;port=7733;dbname=cheyoo',
		'emulatePrepare' => true,
		'username' => 'dbadmin',
		'password' => 'y86tTqbB830P',
		'charset' => 'utf8',
		'class' => 'CDbConnection'
	),
	'db3'=>array(
		'connectionString' => 'mysql:host=119.23.129.226;port=7755;dbname=cheyoo_pay',
		'emulatePrepare' => true,
		'username' => 'dbadmin',
		'password' => 'y86tTqbB830P',
		'charset' => 'utf8',
		'class' => 'CDbConnection'
	),
    //主库 119.23.129.226
	'db5'=>array(
		'connectionString' => 'mysql:host=119.23.129.226;port=7755;dbname=cheyoo_2016',
		'emulatePrepare' => true,
		'username' => 'dbadmin',
		'password' => 'y86tTqbB830P',
		'charset' => 'utf8',
		'class' => 'CDbConnection'
	),
    //从库 119.23.129.226  没有部署
    'db6'=>array(
        'connectionString' => 'mysql:host=119.23.129.226;port=7766;dbname=cheyoo',
        'emulatePrepare' => true,
        'username' => 'dbadmin',
        'password' => 'y86tTqbB830P',
        'charset' => 'utf8',
        'class' => 'CDbConnection'
    ),
    //从库 119.23.129.226
    'db7'=>array(
        'connectionString' => 'mysql:host=119.23.129.226;port=7767;dbname=cheyoo',
        'emulatePrepare' => true,
        'username' => 'dbadmin',
        'password' => 'y86tTqbB830P',
        'charset' => 'utf8',
        'class' => 'CDbConnection'
    ),
    //从库 120.25.217.150
    'db8'=>array(
        'connectionString' => 'mysql:host=10.116.73.230;port=7711;dbname=cheyoo',
        'emulatePrepare' => true,
        'username' => 'dbadmin',
        'password' => 'y86tTqbB830P',
        'charset' => 'utf8',
        'class' => 'CDbConnection'
    ),

	'db9'=>array(
        'connectionString' => 'mysql:host=119.23.129.226;port=7744;dbname=cheyoo_huodong',
        'emulatePrepare' => true,
        'username' => 'dbadmin',
        'password' => 'y86tTqbB830P',
        'charset' => 'utf8',
        'class' => 'CDbConnection'
        ),

	'db_mssql'=>array(
 		'connectionString' => 'sqlsrv:Server=119.10.6.140:1433;Database=Car_DB',
 		'emulatePrepare' => false,
 		'username' => 'sa',
 		'password' => '123@abcCar',
 		'charset' => 'utf8',
 	),
);
