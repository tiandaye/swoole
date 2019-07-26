<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019-07-26
 * Time: 15:45
 */

/**
 * 顺序执行
 */
// bctianwangchongdeMacBook-Pro:coroutine tianwangchong$ time php sample2-1.php
// bc
// real	0m3.054s
// user	0m0.030s
// sys	0m0.016s
function test1()
{
    sleep(1);
    echo "b";
}

function test2()
{
    sleep(2);
    echo "c";
}

test1();
test2();