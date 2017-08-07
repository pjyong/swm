<?php
namespace Cheyoo\System\Component;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;

class App{

    private $container;

    // 这里支持参数传入,或者整个容器实例
    // 比如php api入口传个空的就行了,app api入口倒是可以另外传定制参数
    public function __construct( $container = [] )
    {
        if (is_array($container)) {
            $container = new Container($container);
        }
        if (!$container instanceof ContainerInterface) {
            throw new InvalidArgumentException('必须是一个ContainerInterface实例');
        }
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function start()
    {
        // 所有请求必须带有服务标识
        $serviceName = isset($_GET['m']) ? $_GET['m'] : '';
        if( empty( $serviceName ) ){
            throw new InvalidArgumentException('必须传入服务名称');
        }
        $service = new \Yar_Server( $this->container[$serviceName] );
        $service->handle();
    }
}
