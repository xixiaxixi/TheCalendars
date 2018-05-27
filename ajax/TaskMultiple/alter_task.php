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
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{

    $gid = $_GET['gid'];
    $tid = $_GET['tid'];

    $errors = array();
    require('../../connection.php');

    if (empty($_POST['task_name']))
    {
        $error[] = '需要输入事务名称';
    }
    else
    {
        $tn = $_POST['task_name'];
    }

    if (empty($_POST['date']) || empty($_POST['time']))
    {
        $due = 'none';
    }
    else
    {
        $due = $_POST['date'] . ' ' . $_POST['time'];
    }

    if (empty($_POST['body']))
    {
        $b = 'none';
    }
    else
    {
        $b = $_POST['body'];
    }

    if (!empty($errors))
    {
        echo "error:" . json_encode($errors);
        exit();
    }

    if ($due == 'none' && $b == 'none')
    {
        $q = "UPDATE 任务 SET 任务名='$tn', 截止时间=Now() where 任务代码=$tid";
    }
    else if ($due != 'none' && $b == 'none')
    {
        $q = "UPDATE 任务 SET 任务名='$tn', 截止时间='$due' where 任务代码=$tid";
    }
    else if ($due == 'none' && $b != 'none')
    {
        $q = "UPDATE 任务 SET 任务名='$tn', 任务描述='$b', 截止时间=Now() where 任务代码=$tid";
    }
    else
    {
        $q = "UPDATE 任务 SET 任务名='$tn', 任务描述='$b', 截止时间='$due' where 任务代码=$tid";
    }
    $r = mysqli_query($dbc, $q);
    if (!$r)
    {
        echo "error:" . $q;
        exit();
    }
    $q2 = "DELETE FROM 流程 WHERE 后任务代码='$tid'";
    $r2 = mysqli_query($dbc, $q2);
    if (!$r2)
    {
        echo "error:" . $q2;
        exit();
    }

    $old_participants=array();
    $q7="SELECT DISTINCT 执行者 FROM 用户_任务 WHERE 任务代码='$tid'";
    $r7 = mysqli_query($dbc, $q7);
    if (!$r7)
    {
        echo "error:" . $q7;
        exit();
    }
    while($row7=mysqli_fetch_array($r7))
    {
        if(!in_array($row7[0],$_POST['member']))//删掉被取消勾选的人
        {
            $q2 = "DELETE FROM 用户_任务 WHERE 任务代码='$tid' AND 执行者='{$row7[0]}'";
            $r2 = mysqli_query($dbc, $q2);
            if (!$r2)
            {
                echo "error:" . $q2;
                exit();
            }
        }
        $old_participants[]=$row7[0];//存入已有的所有人
    }



    $q4 = "SET AUTOCOMMIT=0";

    if (isset($_POST['member']) && !empty($_POST['member']))
    {
        foreach ($_POST['member'] as $item)
        {
            if(!in_array($item,$old_participants))//插入没被勾选的人
            {
                $q = "INSERT INTO 用户_任务(执行者,任务代码,所属工程代码) VALUES ('$item', '$tid', '$gid')";
                $r3 = mysqli_query($dbc, $q);
                if (!$r3)
                {
                    $q5 = "ROLLBACK";
                    $r5 = mysqli_query($dbc, $q5);
                    $q5 = "SET AUTOCOMMIT=1";
                    $r5 = mysqli_query($dbc, $q5);
                    echo "error:" . $q;
                    exit();
                }
            }

        }
    }
    $q6 = "COMMIT";
    $r6 = mysqli_query($dbc, $q6);
    if (!$r6)
    {
        echo "error:" . $q6;
        exit();
    }
    if (isset($_POST['task']) && !empty($_POST['task']))
    {
        foreach ($_POST['task'] as $item)
        {
            $q = "INSERT INTO 流程(前任务代码, 后任务代码) VALUES ('$item', '$tid')";
            $r3 = mysqli_query($dbc, $q);
            if (!$r3)
            {
                $q5 = "ROLLBACK";
                $r5 = mysqli_query($dbc, $q5);
                $q5 = "SET AUTOCOMMIT=1";
                $r5 = mysqli_query($dbc, $q5);
                echo "error:" . $q;
                exit();
            }
        }
        $q6 = "COMMIT";
        $r6 = mysqli_query($dbc, $q6);
        if (!$r6)
        {
            echo "error:" . $q6;
            exit();
        }
    }
    $q4 = "SET AUTOCOMMIT=1";
    $r4 = mysqli_query($dbc, $q4);
    if (!$r4)
    {
        echo "error:" . $q4;
        exit();
    }

    require_once('create_json.inc.php');
    create_json_by_projid($gid, $dbc,$redis, 'error_func', '../../'.$path);
    require_once "../../functions/getProjsData.php";
    updateProjData($gid,$con,$redis);
    require_once "WriteChangeLogToRedis.php";
    ProjChangeDataPush($user_id,getProjData($gid,$con,$redis), $tid, 'alter', $redis);
    echo $tid;
    exit();
}
?>


    
		