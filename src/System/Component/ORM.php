<?php
namespace Cheyoo\System\Component;
require_once VENDOR_DIR . 'j4mie/idiorm/idiorm.php';

class ORM{
    public static function init( $configs )
    {
        if( empty( $configs ) ){
            return false;
        }
        try{
            foreach( $configs as $k => $c ){
                \ORM::configure( $c['connectionString'], null, $k );
                \ORM::configure( 'username', $c['username'], $k );
                \ORM::configure( 'password', $c['password'], $k );
                \ORM::configure('return_result_sets', true, $k );
                \ORM::configure('driver_options', array( \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $c['charset'] ), $k );
            }
        } catch( Exception $e){
            echo $e->getMessage();
        }
    }

    public static function __callStatic( $name, $arguments )
    {
        return call_user_func_array( array( '\ORM', $name ), $arguments );
    }

    // 将IdiormResultSet转化为数组
    public static function toArray( \IdiormResultSet $list )
    {
        $results = $list->get_results();
        $arr = array();
		foreach( $results as $orm ){
			$arr[] = $orm->as_array();
		}

		return $arr;
    }
}
