<?php
namespace Cheyoo\Sms;

class Api{
    /**
     * 这里是文档
     * @params
     * @return
     */
    public function sendMsg($firstName, $lastName = "James") {
        echo "已经发送短信了,";
        echo $firstName . ',' . $lastName;
        $moduleName = isset($_GET['m']) ? $_GET['m'] : '';
        return "success";
    }

    public function sendMsgByConcurrent($firstName, $lastName = "James") {
        // 接受到响应的时间
        $startTime = date('Y-m-d H:i:s');
        sleep(3);
        $endTime = date('Y-m-d H:i:s');
        return $firstName.','.$lastName.'于'.$startTime.'开始,于'.$endTime.'结束';
    }
}
