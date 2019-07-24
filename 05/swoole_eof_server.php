<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019-07-24
 * Time: 17:28
 */

// EOF标记协议
class Server
{
    private $serv;

    public function __construct()
    {
        $this->serv = new swoole_server("0.0.0.0", 9501);
        $this->serv->set(array(
            'worker_num'         => 8,
            'daemonize'          => false,
            'max_request'        => 10000,
            'dispatch_mode'      => 2,
            // 数据缓存区的大小
            'package_max_length' => 8192,
            // 指定开启了EOF检测
            'open_eof_check'     => true,
            // 指定了具体的EOF标记
            'package_eof'        => "\r\n"
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
        // 收到了好多数据啊！
        // 这是因为，在Swoole中，采用的不是遍历识别的方法，而只是简单的检查每一次接收到的数据的末尾是不是定义好的EOF标记。因此，在开启EOF检测后，onReceive回调中还是可能会一次收到多个数据包。
        // echo "Get Message From Client {$fd}:{$data}\n";

        // 虽然是多个数据包，但是实际上收到的是N个完整的数据片段，那就只需要根据EOF把每个包再拆出来，一个个处理就好啦。
        $data_list = explode("\r\n", $data);
        foreach ($data_list as $msg) {
            if (!empty($msg)) {
                echo "Get Message From Client {$fd}:{$msg}\n";
            }

        }
    }

    public function onClose($serv, $fd, $from_id)
    {
        echo "Client {$fd} close connection\n";
    }
}

new Server();