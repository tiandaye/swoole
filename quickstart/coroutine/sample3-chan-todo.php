<?php
/**
 * Created by PhpStorm.
 * User: tianwangchong
 * Date: 2019-07-26
 * Time: 15:55
 */

$chan = new chan(3);

// 协程1
go(function () use ($chan) {
    $result = [];
    for ($i = 0; $i < 30; $i++) {
        $result[] = $chan->pop();
    }
    var_dump($result);
});

// 协程2
go(function () use ($chan) {
    for ($i = 0; $i < 10; $i++) {
        $chan->push($i);
    }
});

// 协程3
go(function () use ($chan) {
    for ($i = 10; $i < 20; $i++) {
        $chan->push($i);
    }
});

// 协程4
go(function () use ($chan) {
    for ($i = 20; $i < 30; $i++) {
        $chan->push($i);
    }
});