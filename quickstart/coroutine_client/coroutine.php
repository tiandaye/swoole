<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019-07-26
 * Time: 15:13
 */

$http = new swoole_http_server("0.0.0.0", 9501);

$http->on('request', function ($request, $response) {
    $db = new Swoole\Coroutine\MySQL();
    $db->connect([
        'host'     => '127.0.0.1',
        'port'     => 3306,
        'user'     => 'root',
        'password' => 'root',
        'database' => 'mysql',
    ]);

    $data = $db->query('select * from user');
    $response->end(json_encode($data));
});

$http->start();