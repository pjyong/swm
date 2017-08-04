<?php
namespace Cheyoo\System\Config;

class Env{
    // dev, test, live
    private $env = 'dev';
    private $sysPrefix = 'sys';
    private $config;

    public function loadConfig()
    {
        $sysConfig = require( dirname( __FILE__ ) . '/params_' . $this->env . '.php' );
        $this->set( $this->sysPrefix, $sysConfig );

        return $sysConfig;
    }

    public function isDev()
    {
        return $this->env === 'dev' || $this->env === 'test' ? true : false;
    }

    // 增删改查
    public function exists( $key )
    {
		return isset( $this->config[$key] );
	}

    public function get( $key )
    {
		return $this->config[$key];
	}

    public function set( $key, $obj )
    {
        // 系统配置只能设置一次
        if( $this->exists( $this->sysPrefix ) ) {
            return false;
        }
		return $this->config[$key] = $obj;
	}

    public function clear( $key )
    {
		unset( $this->config[$key] );
	}
}
