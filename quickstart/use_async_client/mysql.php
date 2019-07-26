<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019-07-25
 * Time: 15:21
 */

use Swoole\MySQL;

/**
 * MySQL异步客户端
 */
$server = array(
    'host'     => '127.0.0.1',
    'user'     => 'root',
    'password' => 'root',
    'database' => 'mysql',
);
echo "start \n";
dump(class_exists('Swoole\MySQL'));
$db = new Swoole\MySQL;
echo "end \n";
$db->connect($server, function ($db, $result) {
    echo 2;
    $db->query("show tables", function (Swoole\MySQL $db, $result) {
        echo 3;
        var_dump($result);
        $db->close();
    });
});