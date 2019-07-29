<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019-07-29
 * Time: 15:12
 */

// 在Swoole-2.1.2版本中我们将Server的进程管理模块封装成了PHP类，现在可以在PHP代码中使用Swoole的进程管理器了。
// 在实际项目中经常需要写一些长期运行的脚本，如基于redis、kafka、rabbitmq实现的多进程队列消费者，多进程爬虫等等。程序员需要使用pcntl和posix相关的扩展库实现多进程编程，需要开发者具备深厚的Linux系统编程功底，否则很容易出现问题。
// Swoole提供的进程管理器来自于Swoole\Server，经过大量生产项目验证，稳定性和健壮性都非常高。可大大简化多进程脚本编程工作。


// 一、 创建进程池
// 在PHP代码中使用new Swoole\Process\Pool即可创建一个进程池，构造方法的第一个参数传入工作进程的数量。使用on方法设置WorkerStart即可在工作进程启动时执行指定的代码，可以在这里进行while(true)循环从redis队列中获取任务并处理。使用start方法启动所有进程，管理器开始进入wait状态。
$workerNum = 10;
$pool = new Swoole\Process\Pool($workerNum);

$pool->on("WorkerStart", function ($pool, $workerId) {
    echo "Worker#{$workerId} is started\n";
    $redis = new Redis();
    $redis->pconnect('127.0.0.1', 6379);
    $key = "key1";
    while (true) {
        $msgs = $redis->brpop($key, 2);
        if ($msgs == null) continue;
        var_dump($msgs);
    }
});

$pool->on("WorkerStop", function ($pool, $workerId) {
    echo "Worker#{$workerId} is stopped\n";
});

$pool->start();
// 使用进程管理器，可以保证工作进程的稳定性。
//
// 某个工作进程遇到致命错误、主动退出时管理器会进行回收，避免出现僵尸进程
// 工作进程退出后，管理器会自动拉起、创建一个新的工作进程



// 二、信号处理
// Swoole进程管理器自带了信号处理，向管理器进程发送：
//
// SIGTERM信号：中止服务，向所有工作进程发送SIGTERM关闭进程
// SIGUSR1信号：重启工作进程，管理器会逐个重启工作进程
// 在工作进程中可以配合使用pcntl_signal和pcntl_signal_dispatch实现信号处理。
$pool->on("WorkerStart", function ($pool, $workerId) {
    $running = true;
    pcntl_signal(SIGTERM, function () use (&amp;$running) {
        $running = false;
    });
    echo "Worker#{$workerId} is started\n";
    $redis = new Redis();
    $redis->pconnect('127.0.0.1', 6379);
    $key = "key1";
    while ($running) {
        $msgs = $redis->brpop($key, 2);
        pcntl_signal_dispatch();
        if ( $msgs == null) continue;
        var_dump($msgs);
    }
});





// 三、任务投递
// Swoole进程管理器自带了消息队列和TCP-Socket消息投递的支持。可设置监听系统队列或者TCP端口，接收任务数据。此项功能是可选的，要使用任务投递功能，需要对进程池对象设置onMessage回调。
//
// 消息队列
$pool = new Swoole\Process\Pool(2, SWOOLE_IPC_MSGQUEUE, 0x7000001);

$pool->on("WorkerStart", function ($pool, $workerId) {
    echo "Worker#{$workerId} is started\n";
});

$pool->on("Message", function ($pool, $message) {
    echo "Message: {$message}\n";
});

$pool->start();
// 需要在构造方法的第二个参数传入SWOOLE_IPC_MSGQUEUE，第三个参数设置监听的消息队列KEY。其他程序中使用消息队列相关API就可以向工作进程投递任务了。


// TCP 端口
$pool = new Swoole\Process\Pool(2, SWOOLE_IPC_SOCKET);

$pool->on("WorkerStart", function ($pool, $workerId) {
    echo "Worker#{$workerId} is started\n";
});

$pool->on("Message", function ($pool, $message) {
    echo "Message: {$message}\n";
});

$pool->listen('127.0.0.1', 8089);

$pool->start();
// 使用TCP端口监听，需要设置构造方法的第二个参数为SWOOLE_IPC_SOCKET，并使用listen方法设置监听的主机和端口。
//
// 底层使用了4字节长度+包体的协议。其他程序中向此端口发送数据时，需要在数据包之前增加一个长度字段。

$fp = stream_socket_client("tcp://127.0.0.1:8089", $errno, $errstr) or die("error: $errstr\n");
$msg = json_encode(['data' => 'hello', 'uid' => 1991]);
fwrite($fp, pack('N', strlen($msg)).$msg);
fclose($fp);