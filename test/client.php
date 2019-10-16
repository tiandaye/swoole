<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019-10-16
 * Time: 09:07
 */

/**
 * 异步非阻塞客户端
 */
$client = new Swoole\Client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
$client->on("connect", function (swoole_client $cli) {
    $cli->send("GET / HTTP/1.1\r\n\r\n");
});
$client->on("receive", function (swoole_client $cli, $data) {
    echo "Receive: $data" . PHP_EOL;
    $cli->close(true);
    // $cli->send(str_repeat('A', 100)."\n");
    // sleep(1);
});
$client->on("error", function (swoole_client $cli) {
    echo "error" . PHP_EOL;
});
$client->on("close", function (swoole_client $cli) {
    echo "Connection close" . PHP_EOL;
});
// $client->on("bufferEmpty", function(swoole_client $cli){
//     $cli->close();
// });
$client->connect('127.0.0.1', 9501);

/**
 * 异步
 */
// 配合使用onBufferEmpty，等待发送队列为空时进行close操作
// 协议设计为onReceive收到数据后主动关闭连接，发送数据时对端主动关闭连接
// $client = new swoole_client(SWOOLE_TCP | SWOOLE_ASYNC);
// $client->on("connect", function(swoole_client $cli) {
//
// });
// $client->on("receive", function(swoole_client $cli, $data){
//     $cli->send(str_repeat('A', 1024*1024*4)."\n");
// });
// $client->on("error", function(swoole_client $cli){
//     echo "error\n";
// });
// $client->on("close", function(swoole_client $cli){
//     echo "Connection close\n";
// });
// $client->on("bufferEmpty", function(swoole_client $cli){
//     $cli->close();
// });
// $client->connect('127.0.0.1', 9501);

/**
 * 同步阻塞客户端
 */
// $client = new swoole_client(SWOOLE_SOCK_TCP);
// if (!$client->connect('127.0.0.1', 9501, -1)) {
//     exit("connect failed. Error: {$client->errCode}\n");
// }
// $client->send("hello world\n");
// echo $client->recv();
// $client->close();