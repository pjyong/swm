<?php
namespace Cheyoo\System\Component;

use Cheyoo\System\Config\Env;
class Cy{

    private static $_instance;
    private $moduleName;
    private $env;

    public static function instance()
    {
        if( !( self::$_instance instanceof self ))
        {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    private function __construct()
    {
        $this->env = new Env();
    }

    private function __clone()
    {
        //
    }

    public function getModule()
    {
        $this->moduleName = isset($_GET['m']) ? $_GET['m'] : '';
        if( empty( $this->moduleName ) ){ die('You are going wrong!!!'); }

        return $this->moduleName;
    }

    public function start()
    {
        // 载入配置文件
        $this->env->loadConfig();
        if( $this->env->isDev() ){
            error_reporting(E_ALL);
        } else {
            error_reporting(0);
        }
        $api = '\Cheyoo\\' . $this->getModule() . '\Api';
        $service = new \Yar_Server( new $api() );
        $service->handle();
    }

    public function exists( $key )
    {
		return $this->env->exists( $key );
	}

    public function get( $key )
    {
        return $this->env->get( $key );
	}

    public function set( $key, $obj )
    {
        return $this->env->set( $key, $obj );
	}

    public function clear( $key )
    {
        return $this->env->clear( $key );
	}
}
