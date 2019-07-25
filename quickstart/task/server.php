<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019-07-25
 * Time: 14:25
 */

/**
 * 执行异步任务
 */
// 创建Server对象，监听 127.0.0.1:9501端口
$serv = new swoole_server('127.0.0.1', 9501);

// 设置异步任务的工作进程数量
$serv->set([
    'task_worker_num' => 4
]);

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
    //投递异步任务
    $task_id = $serv->task($data);
    echo "Dispath AsyncTask: id=$task_id\n";

    // $serv->send($fd, "Server: " . $data);
});

// 处理异步任务
$serv->on('task', function ($serv, $task_id, $from_id, $data) {
    echo "New AsyncTask[id=$task_id]" . PHP_EOL;
    // 返回任务执行的结果
    $serv->finish("$data -> OK");

    // // 还可以直接return
    // return 'tiandaye';
});

// 处理异步任务的结果
$serv->on('finish', function ($serv, $task_id, $data) {
    echo "AsyncTask[$task_id] Finish: $data" . PHP_EOL;
});

// 监听连接关闭事件
$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

// 启动服务器
$serv->start();

// 测试:telnet 127.0.0.1 9501