<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019-10-29
 * Time: 09:31
 */

$http = new swoole_http_server('0.0.0.0', 9502);
$http->on('request', function ($request, $response) {
    var_dump($request);
    $response->end("<h1>Hello Swoole. #" . rand(1000, 9999) . "</h1>");
});
$http->start();