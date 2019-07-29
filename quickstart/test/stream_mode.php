<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019-07-29
 * Time: 15:03
 */

// 只适用于同步模式
//
// 单连接并发的同步服务器一般使用dispatch_mode = 3调度请求分配到Worker进程，底层实现使用了忙闲识别方式。
//
// 当Worker进程接收到请求回调onReceive或onRequest时，将Worker进程的状态设置为BUSY，这时Reactor线程将不会再给当前的Worker进程分配新的请求
// 当Worker进程处理完当前的请求后，将状态设置为IDLE，这时Reactor线程将会继续给当前的Worker进程分配新请求
// dispatch_mode = 3忙闲分配模式，在极端情况下所有Worker均处于BUSY时，会退化为dispatch_mode = 1轮询模式。无论Worker进程处于闲还是忙的状态，都会分配到新请求。这样极端情况下，某些请求可能会无法被最快处理。使用dispatch_mode = 3时，需要保证绝大部分时间有充足的空闲Worker。
//
// 在1.9.24版本中底层新增加了Stream模式。将dispatch的过程进行了逆转，Reactor线程不再调度决定向哪个Worker进程投递新请求，而是发起一个Stream的Connect到一个Unix Socket端口。
//
// 空闲的Worker会Accept连接，并接收Reactor传递的新请求
// Worker进程处理请求期间不再Accept，新请求将有其他Worker进行处理
// Worker进程完成请求处理后，直接使用Stream的通道向对应的TCP客户端连接发送结果数据，响应完毕后关闭Reactor和Worker之间的Stream连接
// 新的Stream模式使用配置dispatch_mode = 7来设置开启。此模式的最大优势是：无论任何极端情况下，都可以保证请求会被最快被处理。
$serv = new swoole_server("127.0.0.1", 9501);

$serv->set(array(
    'dispatch_mode' => 7,
    'worker_num'    => 2,
));

$serv->on('receive', function (swoole_server $serv, $fd, $threadId, $data) {
    var_dump($data);
    echo "#{$serv->worker_id}>> received length=" . strlen($data) . "\n";
});

$serv->start();