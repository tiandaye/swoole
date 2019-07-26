<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019-07-26
 * Time: 15:48
 */

use Swoole\Runtime;

/**
 * 并发执行, Swoole\Runtime::enableCoroutine()作用是将PHP提供的stream、sleep、pdo、mysqli、redis等功能从同步阻塞切换为协程的异步IO
 */
// tianwangchongdeMacBook-Pro:coroutine tianwangchong$ time php sample2-2.php
// bc
// real	0m2.051s
// user	0m0.030s
// sys	0m0.016s
Swoole\Runtime::enableCoroutine();
go(function () {
    sleep(1);
    echo "b";
});

go(function () {
    sleep(2);
    echo "c";
});