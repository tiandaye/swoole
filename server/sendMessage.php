<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019-07-30
 * Time: 10:30
 */

/**
 * 此函数可以向任意Worker进程或者Task进程发送消息。在非主进程和管理进程中可调用。收到消息的进程会触发onPipeMessage事件。
 */
$serv = new Swoole\Server("0.0.0.0", 9501);
$serv->set(array(
    'daemonize'     => false,
    'worker_num'      => 2,
    'task_worker_num' => 2,
));
$serv->on('pipeMessage', function ($serv, $src_worker_id, $data) {
    echo "#{$serv->worker_id} message from #$src_worker_id: $data\n";
});
$serv->on('task', function ($serv, $task_id, $reactor_id, $data) {
    var_dump($task_id, $reactor_id, $data);
});
$serv->on('finish', function ($serv, $fd, $reactor_id) {

});
$serv->on('receive', function (swoole_server $serv, $fd, $reactor_id, $data) {
    if (trim($data) == 'task') {
        $serv->task("async task coming");
    } else {
        $worker_id = 1 - $serv->worker_id;
        $serv->sendMessage("hello task process", $worker_id);
    }
});

$serv->start();