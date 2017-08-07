<?php
namespace Cheyoo\Order;

use Cheyoo\System\Component\ORM;
use Cheyoo\System\Component\Tools;

class Api{

    public function getOrdersByUID( $uid )
    {
        global $cy;
        return json_encode($cy->getContainer()['db1']->fetchAll('select * from web_orders where UID = 61200'));
    }
}
