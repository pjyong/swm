<?php
namespace Cheyoo\System\Component;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;


/**
 * 系统默认的一组服务
 */
class DefaultServicesProvider
{
    public function register($container)
    {
        if( $container['settings']['env'] == 'dev' ){
            error_reporting(E_ALL);
        } else {
            error_reporting(0);
        }
        $params = $this->loadConfig( $container['settings']['env'] );
        if( is_array( $params ) && !empty( $params ) ){
            foreach( $params as $k => $v ){
                $container['settings']->set( $k, $v );
            }
        }

        // 在根目录找所有带有api的模块,注册成服务
        $allFiles = scandir( ROOT_DIR );
        $allDirs = array();
        foreach( $allFiles as $v ){
            if( in_array( $v, array( '.', '..' ) ) ){
                continue;
            }
            if( !is_dir( $v ) ){
                continue;
            }
            if( !file_exists( ROOT_DIR . $v . '/' . 'Api.php' ) ){
                continue;
            }
            $container[$v] = function() use( $v ) {
                $api = '\Cheyoo\\' . $v . '\Api';
                return new $api();
            };
        }

        // 继续注册其他服务,比如数据库,缓存等
        if( is_array( $container['settings']['db'] ) && !empty( $container['settings']['db'] ) )
        foreach( $container['settings']['db'] as $k => $dbConfig )
        {
            $container[$k] = function( $container ) use( $dbConfig )
            {
                $config = new Configuration();
                return DriverManager::getConnection( $dbConfig, $config );
            };
        }

        // 载入缓存对象
        // if( !isset( $container['ssdb'] ) ){
        //     $container['ssdb'] = function( $container ){
        //         return new SSDB( $container['settings']['ssdb']['host'], $sysConfig['settings']['ssdb']['port'], $sysConfig['settings']['ssdb']['timeout'] );
        //     };
        // }
    }

    private function loadConfig( $env )
    {
        $configPath = dirname( __FILE__ ) . '/../Config/params_' . $env . '.php';
        if( !file_exists( $configPath ) ){
            return null;
        }
        return require( $configPath );
    }
}
