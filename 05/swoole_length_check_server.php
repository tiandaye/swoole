<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019-07-24
 * Time: 17:42
 */

class Server
{
    private $serv;

    public function __construct()
    {
        $this->serv = new swoole_server("0.0.0.0", 9501);
        $this->serv->set(array(
            'worker_num'            => 8,
            'daemonize'             => false,
            'max_request'           => 10000,
            'dispatch_mode'         => 2,
            // 数据缓存区的大小
            'package_max_length'    => 8192,
            // 打开固定包头协议解析功能
            'open_length_check'     => true,
            // 规定了包头中第几个字节开始是长度字段
            'package_length_offset' => 0,
            // 规定了包头的长度
            'package_body_offset'   => 4,
            // 规定了长度字段的类型
            'package_length_type'   => 'N'
        ));
        $this->serv->on('Start', array($this, 'onStart'));
        $this->serv->on('Connect', array($this, 'onConnect'));
        $this->serv->on('Receive', array($this, 'onReceive'));
        $this->serv->on('Close', array($this, 'onClose'));
        $this->serv->start();
    }

    public function onStart($serv)
    {
        echo "Start\n";
    }

    public function onConnect($serv, $fd, $from_id)
    {
        echo "Client {$fd} connect\n";

    }

    public function onReceive(swoole_server $serv, $fd, $from_id, $data)
    {
        $length = unpack("N", $data)[1];
        echo "Length = {$length}\n";
        $msg = substr($data, -$length);
        echo "Get Message From Client {$fd}:{$msg}\n";
    }

    public function onClose($serv, $fd, $from_id)
    {
        echo "Client {$fd} close connection\n";
    }
}

new Server();