<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019-07-25
 * Time: 13:34
 */

/**
 * 创建UDP服务器
 */
// 创建Server对象，监听 127.0.0.1:9502端口，类型为SWOOLE_SOCK_UDP
$serv = new swoole_server('127.0.0.1', 9502, SWOOLE_PROCESS, SWOOLE_SOCK_UDP);

// 监听数据接收事件
$serv->on('Packet', function ($serv, $data, $clientInfo) {
    $serv->sendto($clientInfo['address'], $clientInfo['port'], "Server " . $data);
    var_dump($clientInfo);
});

// 启动服务器
$serv->start();

//测试:nc -u 127.0.0.1 9502