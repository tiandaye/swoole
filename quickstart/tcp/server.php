<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019-07-25
 * Time: 11:28
 */

/**
 * 创建TCP服务器
 */
// 创建Server对象，监听 127.0.0.1:9501端口
$serv = new swoole_server('127.0.0.1', 9501);

// 在server运行前被调用
$serv->on('start', function ($serv) {
    echo "Start\n";
});

// 监听连接进入事件, 新客户端连接时被调用
$serv->on('connect', function ($serv, $fd, $from_id) {
    echo "Client: no-{$fd} Connect.\n";
});

// 监听数据接收事件
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, "Server: " . $data);
});

// 监听连接关闭事件
$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

// 启动服务器
$serv->start();

// 测试:telnet 127.0.0.1 9501