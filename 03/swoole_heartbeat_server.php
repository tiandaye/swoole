<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019-07-24
 * Time: 15:13
 */

class TimerServer
{
    private $serv;

    public function __construct()
    {
        $this->serv = new swoole_server("0.0.0.0", 9501);
        $this->serv->set(array(
            'worker_num'               => 8,
            'daemonize'                => false,
            'max_request'              => 10000,
            'dispatch_mode'            => 2,
            'debug_mode'               => 1,
            'heartbeat_check_interval' => 10, // 单位:s
            // 'heartbeat_idle_time'      => 20,// 其中heartbeat_idle_time的默认值是heartbeat_check_interval的两倍。 在设置这两个选项后，swoole会在内部启动一个线程，每隔heartbeat_check_interval秒后遍历一次全部连接，检查最近一次发送数据的时间和当前时间的差，如果这个差值大于heartbeat_idle_time，则会强制关闭这个连接，并通过回调onClose通知Server进程。

        ));
        // $this->serv->on('WorkerStart', array($this, 'onWorkerStart'));
        $this->serv->on('Start', array($this, 'onStart'));
        $this->serv->on('Connect', array($this, 'onConnect'));
        $this->serv->on('Receive', array($this, 'onReceive'));
        $this->serv->on('Close', array($this, 'onClose'));
        $this->serv->start();
    }

    // public function onWorkerStart($serv, $worker_id)
    // {
    //     // 在Worker进程开启时绑定定时器
    //     echo "onWorkerStart\n";
    //     // 只有当worker_id为0时才添加定时器,避免重复添加
    //     if ($worker_id == 0) {
    //         $serv->addtimer(5000);
    //     }
    // }

    public function onStart($serv)
    {
        echo "Start \n";
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

new TimerServer();