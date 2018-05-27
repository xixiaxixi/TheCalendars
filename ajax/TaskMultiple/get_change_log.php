<?php
session_start();
if (!isset($_SESSION['id']))
{
    echo "error:非法登录";
    exit();
}
$user_id = $_SESSION['id'];
session_write_close();
set_time_limit(0);
if (!isset($_GET['proj_id']) or empty($_GET['proj_id']))
{
    echo "error:传参失败";
    exit();
}
//echo json_encode($_GET);

$proj_id = $_GET['proj_id'];
require_once "../../connection.php";
$halfsec=0;
while ($sec++<=40)
{
    usleep(500000);
    $key = $user_id . 'projchange' . $proj_id;
    if ($redis->exists($key))
    {
        echo $redis->rPop($key);
        exit();
    }
}
echo json_encode(array('type'=>'none'));