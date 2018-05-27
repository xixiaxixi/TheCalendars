<?php
/*
session_start();
$user_id = 'yin';
echo 'session 就绪';
session_write_close();//
set_time_limit(2);//最长2秒
//echo json_encode($_GET);

$proj_id = 52;
require_once "../connection.php";
while (true)
{
    $key = $user_id . 'projchange' . $proj_id;
    $value = $redis->rPop($key);
    if (false !== $value)
    {
        echo $value;
        exit();
    }
    usleep(300);
}*/

session_start();
$user_id = 'yindaheng';
echo 'session 就绪';
session_write_close();
set_time_limit(30);//最长100秒
//echo json_encode($_GET);

$proj_id = 3;
require_once "../connection.php";
$sec=0;

while ($sec++<=10)
{
    sleep(1);
    $key = $user_id . 'projchange' . $proj_id;
    if ($redis->exists($key))
    {
        echo $redis->rPop($key);
        echo $sec;
        exit();
    }
}
echo "none";