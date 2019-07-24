<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019-07-24
 * Time: 16:46
 */

class Server
{
    private $serv;

    public function __construct()
    {
        $this->serv = new swoole_server("0.0.0.0", 9501);
        $this->serv->set(array(
            'worker_num'    => 8,
            'daemonize'     => false,
            'max_request'   => 10000,
            'dispatch_mode' => 2,
            'debug_mode'    => 1,
        ));
        $this->serv->on('Start', array($this, 'onStart'));
        $this->serv->on('WorkerStart', array($this, 'onWorkerStart'));
        $this->serv->on('Connect', array($this, 'onConnect'));
        $this->serv->on('Receive', array($this, 'onReceive'));
        $this->serv->on('Close', array($this, 'onClose'));
        $this->serv->start();
    }

    public function onStart($serv)
    {
        echo "Start\n";
        // 为当前进程取一个响亮的名字
        cli_set_process_title("reload_master");
    }

    public function onWorkerStart($serv, $worker_id)
    {
        require_once "reload_page.php";
        Test();
    }

    public function onConnect($serv, $fd, $from_id)
    {
        echo "Client {$fd} connect\n";

    }

    public function onReceive(swoole_server $serv, $fd, $from_id, $data)
    {
        echo "Get Message From Client {$fd}:{$data}\n";
    }

    public function onClose($serv, $fd, $from_id)
    {
        echo "Client {$fd} close connection\n";
    }
}

new Server();