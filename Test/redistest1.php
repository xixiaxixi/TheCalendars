<?php
require_once "../connection.php";
require_once "../ajax/TaskMultiple/WriteChangeLogToRedis.php";
require_once "../functions/getProjsData.php";
ProjChangeDataPush('lzq',getProjData(51,$con,$redis), 249, 'alter', $redis);
$key='testtestte' . 'projchange' . 51;
//echo $redis->rPop($key);//用户名+projchange+工程id=>修改情况

exit();