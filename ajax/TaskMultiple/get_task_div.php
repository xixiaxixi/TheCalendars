<?php
session_start();
if (!isset($_SESSION['id']))
{
    echo 'error:非法登录';
    exit();
}
$user_id=$_SESSION['id'];
session_write_close();
?>

<?php
if (isset($_POST['proj_id']) and isset($_POST['proj_id']) and !empty($_POST['proj_id']) and !empty($_POST['task_id']))
{
    $proj_id = $_POST['proj_id'];
    $task_id = $_POST['task_id'];
    require_once "../../connection.php";
    require_once "../../functions/getProjsData.php";
    $proj = getProjData($proj_id, $con, $redis);
    $task = $proj['tasks'][$task_id];
    require_once "../../Task/functions/TaskDiv.php";
    if ($task['maker'] == $user_id)
        task_div($proj, $task);
    else
        viewonly_task_div($proj, $task);
    exit();
}
else
{
    echo "error:传参失败";
    exit();
}
?>
