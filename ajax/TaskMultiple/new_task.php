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
        $d = 'none';
    }
    else
    {
        $d = $_POST['date'] . ' ' . $_POST['time'];
    }

    if (empty($_POST['body']))
    {
        $b = NULL;
    }
    else
    {
        $b = $_POST['body'];
    }

    if (!empty($errors))
    {
        echo 'error:' . json_encode($errors);
        exit();
    }

    if ($d == 'none')
    {
        $q = "INSERT INTO 任务(任务名, 任务描述, 创建者, 截止时间, 所属工程代码) VALUES ('$tn', '$b', '$user_id', Now(), '$gid')";
    }
    else
    {
        $due = $d;
        $q = "INSERT INTO 任务(任务名, 任务描述, 创建者, 截止时间, 所属工程代码) VALUES ('$tn', '$b', '$user_id', '$due', '$gid')";
    }
    $r = mysqli_query($dbc, $q);
    if (!$r)
    {
        echo 'error:' . $q;
        exit();
    }
    $q2 = "SELECT MAX(任务代码) FROM 任务 WHERE 创建者='$user_id'";
    $r2 = mysqli_query($dbc, $q2);
    if (!$r2)
    {
        echo 'error:' . $q2;
        exit();
    }
    $tid = mysqli_fetch_array($r2)[0];
    $q4 = "SET AUTOCOMMIT=0";
    $r4 = mysqli_query($dbc, $q4);
    if (!$r4)
    {
        echo "error:" . $q4;
        exit();
    }

    if (isset($_POST['member']) && !empty($_POST['member']))
    {
        foreach ($_POST['member'] as $item)
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
        $q6 = "COMMIT";
        $r6 = mysqli_query($dbc, $q6);
        if (!$r6)
        {
            echo "error:" . $q6;
            exit();
        }
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
            echo 'error:' . $q6;
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
    ProjChangeDataPush($user_id,getProjData($gid,$con,$redis), $tid, 'new', $redis);
    echo $tid;
    exit();
}
?>


    
		