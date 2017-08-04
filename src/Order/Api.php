<?php
namespace Cheyoo\Order;

use Cheyoo\System\Component\ORM;
use Cheyoo\System\Component\Tools;

class Api{
    
    public function getOrdersByUID( $uid )
    {
        $allOrders = ORM::forTable( 'web_orders', 'db1' )->where( 'UID', $uid )->findMany();
        return ORM::toArray( $allOrders );
    }
}
