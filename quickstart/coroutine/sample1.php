<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019-07-26
 * Time: 15:25
 */


$c = 10;
while ($c--) {
    go(function () {
        // 这里使用 sleep 5 来模拟一个很长的命令
        // co::exec("sleep 5")

        // 返回值
        // Co::exec执行完成后会恢复挂起的协程，并返回命令的输出和退出的状态码。
        var_dump(co::exec("sleep 5"));
    });
}