<?php
session_start();
if (!isset($_SESSION['id']))
{
    echo "error:非法登录";
    exit();
}
$user_id=$_SESSION['id'];
$path=$_SESSION['canvas_json_path'];
$projs=$_SESSION['projects'];
session_write_close();
?>

<?php
if (isset($_POST['proj_id'])and!empty($_POST['proj_id']))
{
    $proj_id = $_POST['proj_id'];
    require_once('../connection.php');
    $q = "DELETE FROM 工程 WHERE 工程代码 = '$proj_id'";
    $r = mysqli_query($dbc, $q);
    if (!$r)
    {
        echo "error:".$q;
        exit();
    }

    require_once '../connection.php';
    unlink('../'.$path.$proj_id.'.json');
    $redis->del('canvasdata'.$proj_id);
    unset($projs[$proj_id]);
    $redis->del('projectdata'.$proj_id);
    echo true;
    exit();
}
else
{
    echo "error:传参失败";
    exit();
}
?>