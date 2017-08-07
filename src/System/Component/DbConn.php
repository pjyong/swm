<?php
namespace Cheyoo\System\Component;

class DbConn{
    private $container;

    public function setContainer( $container )
    {
        $this->container = $container;
        return $this;
    }
}
