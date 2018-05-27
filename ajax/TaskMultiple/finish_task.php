<?php
session_start();
if (!isset($_SESSION['id']))
{
    echo "error:非法登录";
    exit();
}
$user_id=$_SESSION['id'];
$path=$_SESSION['canvas_json_path'];
session_write_close();
?>

<?php
if (!empty($_POST['tid']) && !empty($_POST['pid']))
{
    $tid = $_POST['tid'];
    $pid = $_POST['pid'];
    require('../../connection.php');
    require('create_json.inc.php');
    $ddl=date("Y-m-d H:i:s",strtotime('-1 second'));
    $q = "UPDATE 任务 SET 截止时间='$ddl' WHERE 任务代码 = $tid;";
    $r = mysqli_query($dbc, $q);
    if (!$r)
    {
        echo "error:".$q;
        exit();
    }

    require_once('create_json.inc.php');
    create_json_by_projid($pid, $dbc,$redis, 'error_func', '../../'.$path);
    require_once "../../functions/getProjsData.php";
    updateProjData($pid,$con,$redis);
    require_once "WriteChangeLogToRedis.php";
    ProjChangeDataPush($user_id,getProjData($pid,$con,$redis), $tid, 'finish', $redis);
    echo $ddl;
    exit();
}
else
{
    echo "error:传参失败";
    exit();
}
?>