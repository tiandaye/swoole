<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019-07-26
 * Time: 16:03
 */

Swoole\Runtime::enableCoroutine();

/**
 * 延迟任务，在协程退出时执行，先进后出
 */
go(function () {
    echo "a";
    defer(function () {
        echo "~a";
    });
    echo "b";
    defer(function () {
        echo "~b";
    });
    sleep(1);
    echo "c";
});