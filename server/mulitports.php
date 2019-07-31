<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019-07-30
 * Time: 18:47
 */

// register_shutdown_function('handleFatal', $serv, $fd);
// function handleFatal()
// {
//     $error = error_get_last($serv, $fd);
//     if (isset($error['type'])) {
//         switch ($error['type']) {
//             case E_ERROR :
//             case E_PARSE :
//             case E_CORE_ERROR :
//             case E_COMPILE_ERROR :
//                 $message = $error['message'];
//                 $file = $error['file'];
//                 $line = $error['line'];
//                 $log = "$message ($file:$line)\nStack trace:\n";
//                 $trace = debug_backtrace();
//                 foreach ($trace as $i => $t) {
//                     if (!isset($t['file'])) {
//                         $t['file'] = 'unknown';
//                     }
//                     if (!isset($t['line'])) {
//                         $t['line'] = 0;
//                     }
//                     if (!isset($t['function'])) {
//                         $t['function'] = 'unknown';
//                     }
//                     $log .= "#$i {$t['file']}({$t['line']}): ";
//                     if (isset($t['object']) and is_object($t['object'])) {
//                         $log .= get_class($t['object']) . '->';
//                     }
//                     $log .= "{$t['function']}()\n";
//                 }
//                 if (isset($_SERVER['REQUEST_URI'])) {
//                     $log .= '[QUERY] ' . $_SERVER['REQUEST_URI'];
//                 }
//                 error_log($log);
//                 $serv->send($fd, $log);
//             default:
//                 break;
//         }
//     }
// }

$serv = new swoole_server("0.0.0.0", 9501);
//这里监听了一个UDP端口用来做内网管理
$serv->addlistener('127.0.0.1', 9502, SWOOLE_SOCK_UDP);
$serv->on('connect', function ($serv, $fd) {
    echo "Client:Connect.\n";
});
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    $info = $serv->connection_info($fd, $from_id);
    //来自9502的内网管理端口
    if ($info['server_port'] == 9502) {
        $serv->send($fd, "welcome admin\n");
    } //来自外网
    else {
        $serv->send($fd, 'Swoole: ' . $data);
    }
});
$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});
$serv->start();