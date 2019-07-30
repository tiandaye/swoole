<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019-07-30
 * Time: 13:48
 */

// // 调用此方法可以得到底层的socket句柄，返回的对象为sockets资源句柄。
// // 此方法需要依赖PHP的sockets扩展，并且编译swoole时需要开启--enable-sockets选项
// // 使用socket_set_option函数可以设置更底层的一些socket参数。
// $socket = $server->getSocket();
// if (!socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1)) {
//     echo 'Unable to set option on socket: '. socket_strerror(socket_last_error()) . PHP_EOL;
// }

// // 监听端口
// // 使用listen方法增加的端口，可以使用Swoole\Server\Port对象提供的getSocket方法。
// $port = $server->listen('127.0.0.1', 9502, SWOOLE_SOCK_TCP);
// $socket = $port->getSocket();

// 支持组播
// 使用socket_set_option设置MCAST_JOIN_GROUP参数可以将Socket加入组播，监听网络组播数据包。
$server = new swoole_server('0.0.0.0', 9905, SWOOLE_BASE, SWOOLE_SOCK_UDP);
$server->set(['worker_num' => 1]);
$socket = $server->getSocket();

$ret = socket_set_option(
    $socket,
    IPPROTO_IP,
    MCAST_JOIN_GROUP,
    array('group' => '224.10.20.30', 'interface' => 'eth0')
);

if ($ret === false) {
    throw new RuntimeException('Unable to join multicast group');
}

$server->on('Packet', function (swoole_server $serv, $data, $addr) {
    $serv->sendto($addr['address'], $addr['port'], "Swoole: $data");
    var_dump($addr, strlen($data));
});

$server->start();
// group 表示组播地址
// interface 表示网络接口的名称，可以为数字或字符串，如eth0、wlan0