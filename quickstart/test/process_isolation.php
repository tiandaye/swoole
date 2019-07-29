<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019-07-29
 * Time: 10:58
 */

// $server = new Swoole\Http\Server('127.0.0.1', 9500);
//
// $i = 1;
//
// $server->on('Request', function ($request, $response) {
//     global $i;
//     $response->end($i++);
// });
//
// $server->start();


// 在多进程的服务器中，$i变量虽然是全局变量(global)，但由于进程隔离的原因。假设有4个工作进程，在进程1中进行$i++，实际上只有进程1中的$i变成2了，其他另外3个进程内$i变量的值还是1。
// 正确的做法是使用Swoole提供的Swoole\Atomic或Swoole\Table数据结构来保存数据。如上述代码可以使用Swoole\Atomic实现。
$server = new Swoole\Http\Server('127.0.0.1', 9500);

$atomic = new Swoole\Atomic(1);

$server->on('Request', function ($request, $response) use ($atomic) {
    $response->end($atomic->add(1));
});

$server->start();