<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019-07-30
 * Time: 13:32
 */

// 并发执行多个task异步任务
$tasks[] = mt_rand(1000, 9999); //任务1
$tasks[] = mt_rand(1000, 9999); //任务2
$tasks[] = mt_rand(1000, 9999); //任务3
var_dump($tasks);

//等待所有Task结果返回，超时为10s
$results = $serv->taskWaitMulti($tasks, 10.0);

if (!isset($results[0])) {
    echo "任务1执行超时了\n";
}
if (isset($results[1])) {
    echo "任务2的执行结果为{$results[1]}\n";
}
if (isset($results[2])) {
    echo "任务3的执行结果为{$results[2]}\n";
}