<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019-07-29
 * Time: 15:54
 */

function my_onStart($serv)
{
    // 属性列表
    echo $serv->manager_pid;  //管理进程的PID，通过向管理进程发送SIGUSR1信号可实现柔性重启
    echo "\n";
    echo $serv->master_pid;  //主进程的PID，通过向主进程发送SIGTERM信号可安全关闭服务器
    echo "\n";
    echo $serv->connections; //当前服务器的客户端连接，可使用foreach遍历所有连接
    echo "\n";
    echo "Start\n";
}

function my_onConnect($serv, $fd, $from_id)
{

}

function my_onReceive($serv, $fd, $from_id)
{

}

function my_onClose($serv, $fd, $from_id)
{

}

// 构建Server对象
$serv = new Swoole\Server('0.0.0.0', 9501, SWOOLE_BASE, SWOOLE_SOCK_TCP);

// 设置运行时参数
$serv->set(array(
    'worker_num' => 4,
    'daemonize'  => false,
    'backlog'    => 128,
));

// 注册事件回调函数
$serv->on('Start', 'my_onStart');
$serv->on('Connect', 'my_onConnect');
$serv->on('Receive', 'my_onReceive');
$serv->on('Close', 'my_onClose');

// 启动服务器
$serv->start();