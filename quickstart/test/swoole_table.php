<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019-07-29
 * Time: 14:13
 */

/**
 * task中使用table
 */
class server
{
    private $ws;

    public function getlist($ws, $fd)
    {
        $list = [];
        foreach ($this->ws->userList as $k => $v) {
            $list[$k] = $v;
        }
        $ws->push($fd, json_encode($list));
        return $list;
    }

    public function setlist($uid)
    {
        $this->ws->userList->set('x' . $uid, array("gid" => $uid, "fd" => $uid, 'info' => $uid));
    }

    public function closeClient($ws, $fd)
    {
        $this->ws->userList->del('x' . $fd);
    }

    public function start()
    {
        $this->ws = new swoole_websocket_server("0.0.0.0", 5050);
        $this->ws->userList = new swoole_table(10240);
        $this->ws->userList->column('gid', swoole_table::TYPE_STRING, 32);
        $this->ws->userList->column('fd', swoole_table::TYPE_INT);
        $this->ws->userList->column('info', swoole_table::TYPE_STRING, 1024 * 10);
        $this->ws->userList->create();

        $this->ws->on('message', function ($ws, $frame) {
            print_r($frame);
            $ws->task($frame);
        });
        $this->ws->on('task', function ($ws, $task_id, $from_id, $frame) {
            $this->setlist($frame->fd);
            print_r($this->getlist($ws, $frame->fd));
            return;
        });
        $this->ws->on('close', function ($ws, $fd) {
            $this->closeClient($ws, $fd);
        });

        $this->ws->on('finish', function ($ws, $task_id, $data) {
        });
        $this->ws->set(array('task_worker_num' => 16));
        $this->ws->start();

    }
}

$server = new server();
$server->start();

// 用这个可以
// var socket = new WebSocket('ws://127.0.0.1:5050');
// // 打开Socket
// socket . onopen = function (event) {
//     // 发送一个初始化消息
//     socket . send('I am the client and I\'m listening!');
//     // 监听消息
//     socket . onmessage = function (event) {
//         console . log('Client received a message', event);
//     };
//     // 监听Socket的关闭
//     socket . onclose = function (event) {
//         console . log('Client notified socket has closed', event);
//     };
//     socket . send('hehhe');
//     // 关闭Socket....
//     //        socket.close()
// };

// // 使用
// var wsServer = 'ws://127.0.0.1:5050';
// var websocket = new WebSocket(wsServer);
// websocket.onopen = function (evt) {
//     console.log("Connected to WebSocket server.");
// };
//
// websocket.onclose = function (evt) {
//     console.log("Disconnected");
// };
//
// websocket.onmessage = function (evt) {
//     console.log('Retrieved data from server: ' + evt.data);
// };
//
// websocket.onerror = function (evt, e) {
//     console.log('Error occured: ' + evt.data);
// };
// websocket.send(123);






// $table = new swoole_table(1024);
// $table->column('fd', swoole_table::TYPE_INT);
// $table->column('from_id', swoole_table::TYPE_INT);
// $table->column('data', swoole_table::TYPE_STRING, 64);
// $table->create();
//
// $serv = new swoole_server('127.0.0.1', 9501);
// //将table保存在serv对象上
// $serv->table = $table;
//
// $serv->on('receive', function ($serv, $fd, $from_id, $data) {
//     $ret = $serv->table->set($fd, array('from_id' => $data, 'fd' => $fd, 'data' => $data));
// });
//
// $serv->start();