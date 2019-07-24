<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019-07-24
 * Time: 14:54
 */

class TimerServer
{
    private $serv;

    public function __construct()
    {
        $this->serv = new swoole_server('0.0.0.0', 9501);

        $this->serv->set([
            'worker_num'    => 8,
            'daemonize'     => false,
            'max_request'   => 10000,
            'dispatch_mode' => 2,
        ]);

        $this->serv->on('Start', [$this, 'onStart']);
        $this->serv->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->serv->on('Connect', [$this, 'onConnect']);
        $this->serv->on('Receive', [$this, 'onReceive']);
        $this->serv->on('Close', [$this, 'onClose']);

        // bind callbask
        $this->serv->on('Timer', [$this, 'onTimer']);

        $this->serv->start();
    }

    public function onStart($serv)
    {
        echo "Start\n";
    }

    public function onWorkerStart($serv, $worker_id)
    {
        // 在Worker进程开启时绑定定时器
        echo "onWorkerStart\n";
        // 只有当worker_id为0时才添加定时器,避免重复添加
        if ($worker_id == 0) {
            $serv->addTimer(500);
            $serv->addTimer(1000);
            $serv->addTimer(1500);
        }
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

    public function onTimer($serv, $interval)
    {
        switch ($interval) {
            case 500:
                {    //
                    echo "Do Thing A at interval 500\n";
                    break;
                }
            case 1000:
                {
                    echo "Do Thing B at interval 1000\n";
                    break;
                }
            case 100:
                {
                    echo "Do Thing C at interval 100\n";
                    break;
                }
        }
    }
}

new TimerServer();