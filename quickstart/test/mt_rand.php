<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019-07-29
 * Time: 10:47
 */

// 在Swoole中如果在父进程内调用了mt_rand，不同的子进程内再调用mt_rand返回的结果会是相同的。所以必须在每个子进程内调用mt_srand重新播种。
// shuffle和array_rand等依赖随机数的PHP函数同样会受到影响

mt_rand(0, 1);
// 开始
$worker_num = 16;

// fork 进程
for ($i = 0; $i < $worker_num; $i++) {
    $process = new swoole_process('child_async', false, 2);
    $pid = $process->start();
}

//异步执行进程
function child_async(swoole_process $worker)
{
    // 没有这行代码， 随机数将会是一样的
    mt_srand();
    echo mt_rand(0, 100) . PHP_EOL;
    $worker->exit();
}