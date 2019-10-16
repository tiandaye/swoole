<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019-10-16
 * Time: 09:07
 */


// 创建server对象, 监听对应服务器端口
$serv = new swoole_server('0.0.0.0', 9501);

// 监听连接进入事件
$serv->on('connect', function ($serv, $fd) {
    echo "client:connect.\n";
    // $serv->send($fd, 'server:' . 'hello');
});

// 监听数据接收事件
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, 'server:' . $data);
});

// 监听连接关闭事件
$serv->on('close', function ($serv, $fd) {
    echo "client:close.\n";
});

// 启动服务器
$serv->start();