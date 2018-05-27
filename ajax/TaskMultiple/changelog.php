<?php
session_start();
if (!isset($_SESSION['id']))
{
    echo "error:非法登录";
    exit();
}
$user_id = $_SESSION['id'];
session_write_close();
//set_time_limit(2);//最长100秒
if (!isset($_GET['proj_id']) or empty($_GET['proj_id']))
{
    echo "error:传参失败";
    exit();
}
//echo json_encode($_GET);

$proj_id = $_GET['proj_id'];
require_once "../../connection.php";
$key = $user_id . 'projchange' . $proj_id;
if ($redis->exists($key))
    echo $redis->rPop($key);
else
    echo json_encode(array('type' => 'none'));
exit();