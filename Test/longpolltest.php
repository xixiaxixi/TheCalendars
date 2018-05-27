<?php
if(!$_GET['timed']) exit();
date_default_timezone_set("PRC");
set_time_limit(0);//无限请求超时时间
$timed = $_GET['timed'];
while (true) {
    sleep(3); // 休眠3秒，模拟处理业务等
    $i = rand(0,100); // 产生一个0-100之间的随机数
    if ($i > 20 && $i < 56) { // 如果随机数在20-56之间就视为有效数据，模拟数据发生变化
        $responseTime = time();
        // 返回数据信息，请求时间、返回数据时间、耗时
        echo ("result: " . $i . ", response time: " . $responseTime . ", request time: " . $timed . ", use time: " . ($responseTime - $timed));
        exit();
    } else { // 模拟没有数据变化，将休眠 hold住连接
        sleep(13);
        exit();
    }
}