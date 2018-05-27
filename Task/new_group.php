<?php
session_start();
if (!isset($_SESSION['id']))
{
    echo '<script type="text/javascript">alert("非法登录!");</script>';
    echo '<script type="text/javascript">window.location.replace("../login.html");</script>';
    exit();
}
else
{
    $id = $_SESSION['id'];
}
?>

<?php
require('../connection.php');
if ($_GET['action'] == 'new')
{
    $_SESSION['action'] = 'new';
}
if ($_GET['action'] == 'alter')
{
    $_SESSION['action'] = 'alter';
    $_SESSION['alter_proj_id'] = $_GET['proj_id'];
}
if ($_SESSION['action'] == 'new' && $_SERVER['REQUEST_METHOD'] == 'POST')
{
    unset($_SESSION['action']);
    if (!empty($_POST['group_name']))
    {
        $gn = mysqli_real_escape_string($dbc, trim($_POST['group_name']));
        $r = mysqli_query($dbc, "SELECT MAX(工程代码) FROM 工程 LIMIT 1");
        if (mysqli_num_rows($r) == 1)
        {
            $gid = mysqli_fetch_array($r, MYSQLI_NUM)[0] + 1;
            $r0 = mysqli_query($dbc, "INSERT INTO 工程(工程代码, 工程名, 创建者, 工程描述) VALUES ('$gid', '$gn', '$id', '{$_POST['group_description']}')");
            if (!$r0)
            {
                echo 'error:' . "INSERT INTO 工程(工程代码, 工程名, 创建者) VALUES ('$gid', '$gn', '$id')";
                exit();
            }
            //$r = mysqli_query($dbc, "INSERT INTO 任务(任务名,创建者,截止时间, 所属工程代码) VALUES ('默认任务', '$id',NOW(), '$gid')");
            //if(!$r){ echo '<p>' . mysqli_error($dbc) . '</p>'; }
            $r2 = mysqli_query($dbc, "SELECT 任务代码 FROM 任务 WHERE 所属工程代码 = '$gid' LIMIT 1");
            if (!$r2)
            {
                echo 'error:' . "SELECT 任务代码 FROM 任务 WHERE 所属工程代码 = '$gid' LIMIT 1";
                exit();
            }

            if ($r0 && $r && $r2)
            {
                $tid = mysqli_fetch_array($r2, MYSQLI_NUM)[0];
                $q4 = "SET AUTOCOMMIT=0";
                $r4 = mysqli_query($dbc, $q4);
                if (!$r4)
                {
                    echo 'error:' . $q4;
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
                $i = 0;
                $name = "other$i";
                while (isset($_POST[$name]) AND !empty($_POST[$name]))
                {
                    $mn = $_POST[$name];
                    $q = "INSERT INTO 用户_任务(执行者,任务代码,所属工程代码) VALUES ('$mn', '$tid', '$gid')";
                    $r3 = mysqli_query($dbc, $q);
                    $i++;
                    $name = "other$i";
                    if (!$r3)
                    {
                        $q5 = "ROLLBACK";
                        $r5 = mysqli_query($dbc, $q5);
                        $q5 = "SET AUTOCOMMIT=1";
                        $r5 = mysqli_query($dbc, $q5);
                        echo 'error:' . $q;
                        exit();
                    }
                }
                $q6 = "COMMIT";
                $r6 = mysqli_query($dbc, $q6);
                if ($r6)
                {
                    header("Refresh:0,Url=Project.php?proj_id=" . $gid);
                    exit();
                }
                else
                {
                    echo 'error:' . $q6;
                    exit();
                }
            }
            else
            {
                echo '<p>error: r&r2' . mysqli_error($dbc) . '</p>';
            }
        }
        else
        {
            echo '<script type="text/javascript">alert("创建失败6!请重试");</script>';
            echo '<script type="text/javascript">window.location.replace("new_group.php");</script>';
            exit();
        }#在表中插入
    }
    else
    {
        $errors = "请输入工程组名!";
    }
    if (isset($errors))
    {
        echo "<script type=\"text/javascript\">alert(\"$errors\");</script>";
        echo '<script type="text/javascript">window.location.replace("new_group.php");</script>';
        exit();
    }
    else
    {
        echo '<script type="text/javascript">alert("创建失败7!请重试");</script>';
        echo '<script type="text/javascript">window.location.replace("new_group.php");</script>';
        exit();
    }
}
if ($_SESSION['action'] == 'alter' && $_SERVER['REQUEST_METHOD'] == 'POST')
{
    unset($_SESSION['action']);
    $gid = $_SESSION['alter_proj_id'];
    unset($_SESSION['alter_proj_id']);
    $q = "SELECT 任务代码 FROM 任务 WHERE 所属工程代码='$gid' AND 创建者='default'";
    $r2 = mysqli_query($dbc, $q);
    if (!$r2)
    {
        echo 'error: ' . $q;
        exit();
    }
    $tid = mysqli_fetch_array($r2)[0];
    $q = "UPDATE 工程 SET 工程名='{$_POST['group_name']}', 工程描述='{$_POST['group_description']}' WHERE 工程代码='$gid'";
    $r = mysqli_query($dbc, $q);
    if (!$r)
    {
        echo 'error: ' . $q;
        exit();
    }
    $q = "DELETE FROM 用户_任务 WHERE 任务代码='$tid' AND 执行者!='{$_SESSION['id']}'";
    $r = mysqli_query($dbc, $q);
    if (!$r)
    {
        echo 'error: ' . $q;
        exit();
    }
    $q4 = "SET AUTOCOMMIT=0";
    $r4 = mysqli_query($dbc, $q4);
    if (!$r4)
    {
        echo 'error:' . $q4;
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
    $i = 0;
    $name = "other$i";
    while (isset($_POST[$name]) AND !empty($_POST[$name]))
    {
        $mn = $_POST[$name];
        $q = "INSERT INTO 用户_任务(执行者,任务代码,所属工程代码) VALUES ('$mn', '$tid', '$gid')";
        $r3 = mysqli_query($dbc, $q);
        $i++;
        $name = "other$i";
        if (!$r3)
        {
            $q5 = "ROLLBACK";
            $r5 = mysqli_query($dbc, $q5);
            $q5 = "SET AUTOCOMMIT=1";
            $r5 = mysqli_query($dbc, $q5);
            echo 'error:' . $q;
            exit();
        }
    }
    $q6 = "COMMIT";
    $r6 = mysqli_query($dbc, $q6);
    if ($r6)
    {
        require_once "../functions/getProjsData.php";
        updateProjData($gid, $con, $redis);
        header("Refresh:0,Url=Project.php?proj_id=" . $gid);
        exit();
    }
    else
    {
        echo 'error:' . $q6;
        exit();
    }
}
?>

<?php
require_once "../functions/getProjsData.php";
$proj = array();
if ($_GET['action'] == 'alter' and !empty($_GET['proj_id']))
    $proj = getProjData($_GET['proj_id'], $con, $redis);
$page_title = $_GET['action'] == 'alter' ? "Alter Project" : "New Project";
$css = '<link rel="stylesheet" type="text/css" href="css/AddProject.css"/>';
$script = '<script src="../lib/jquery1.8.3.min.js"></script>';
include('header.html');
?>
    <div class="swiper-container" id="swiper-container">
        <div class="swiper-wrapper" id="swiper-wrapper">
            <div class="swiper-slide">
                <div class="new-div">
                    <h1 class="head">
                        <?php
                        if ($_GET['action'] == 'alter' and !empty($_GET['proj_id']))
                            echo "Alter the Project {$proj['name']}";
                        else
                            echo "Create a New Project";
                        ?>
                    </h1>
                    <form action="new_group.php" method="post">
                        <div class="form_element_input text_part">
                            <input class="input-text" type="text"
                                   placeholder="Project Name" value=
                                   <?php
                                   if ($_GET['action'] == 'alter' and !empty($_GET['proj_id']))
                                       echo $proj['name'];
                                   else
                                       echo "''";
                                   ?>
                                   name="group_name" id="Name">
                        </div>
                        <div class="form_element_textarea text_part">
                        <textarea class="text-area" placeholder="Project Description" name="group_description"
                                  id="Name"><?php
                            if ($_GET['action'] == 'alter' and !empty($_GET['proj_id']))
                                echo $proj['description'];
                            ?></textarea>
                        </div>
                        <fieldset>
                            <legend>Participants</legend>
                            <?php
                            $checked_mem = array();
                            if ($_GET['action'] == 'alter' and !empty($_GET['proj_id']))
                                foreach ($proj['participants'] as $key => $value)
                                    $checked_mem[] = $key;
                            $q = "SELECT DISTINCT 用户名,姓名 FROM 用户 RIGHT JOIN(SELECT DISTINCT 执行者 FROM 用户_任务 RIGHT JOIN(SELECT 所属工程代码 FROM 用户_任务 WHERE 执行者='{$_SESSION['id']}')AS tt USING(所属工程代码)) AS t ON t.执行者=用户.用户名 WHERE 用户名!='{$_SESSION['id']}'";
                            $r = mysqli_query($dbc, $q);
                            $used_id = array();
                            $q1 = "SELECT 用户名,姓名 FROM 用户 WHERE 用户名='{$_SESSION['id']}'";
                            $r1 = mysqli_query($dbc, $q1);
                            while ($row1 = @mysqli_fetch_array($r1))
                            {
                                $name1 = empty($row1['姓名']) ? $row1['用户名'] : $row1['姓名'];
                                echo "<input type='checkbox' name='member[]' value='{$_SESSION['id']}' id='{$_SESSION['id']}' checked='checked' disabled='disabled'/>";
                                echo "<label for='{$_SESSION['id']}'>$name1</label><br>";
                            }
                            while ($row = @mysqli_fetch_array($r))
                            {
                                if (in_array($row['用户名'], $checked_mem))
                                    echo "<input type='checkbox' name='member[]' value='{$row['用户名']}' id='{$row['用户名']}' checked='checked'/>";
                                else
                                    echo "<input type='checkbox' name='member[]' value='{$row['用户名']}' id='{$row['用户名']}'/>";
                                $name = empty($row['姓名']) ? $row['用户名'] : $row['姓名'];
                                echo "<label for='{$row['用户名']}'>$name</label><br>";
                            }
                            ?>
                            <div class="form_element_input text_part">
                                <input class="input-text" type="text" placeholder="Another Member You Want to Add"
                                       id="Name">
                                <button type="button" class="search_button" id="search_button">Search</button>
                            </div>
                        </fieldset>
                        <input type="submit" value=
                        <?php
                        if ($_GET['action'] == 'alter' and !empty($_GET['proj_id']))
                            echo "Alter!";
                        else
                            echo "Create!";
                        ?> class="button_part">
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php
session_write_close();
$script = '<script src="js/AddProjPageJS.js"></script>';
include('footer.html');
?>