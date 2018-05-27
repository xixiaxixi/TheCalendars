<?php
session_start();
if ($_GET['action'] == 'logout')
{
    session_start();
    unset($_SESSION['id']);
    session_write_close();
    setcookie('logname', '', time() - 10);
    setcookie('logpass', '', time() - 10);
    echo '<script>alert("注销成功")</script>';
    echo '<script type="text/javascript">window.location.replace("login.html");</script>';
    exit();
}
if (!isset($_POST['logname']) or empty($_POST['logname']))
{
    echo '<script>alert("请输入用户名")</script>';
    echo '<script type="text/javascript">window.location.replace("login.html");</script>';
}
elseif (!isset($_POST['logpass']) or empty($_POST['logpass']))
{
    echo '<script>alert("请输入密码")</script>';
    echo '<script type="text/javascript">window.location.replace("login.html");</script>';
}
else
{
    $logname = htmlspecialchars($_POST['logname']);
    $logpass = md5($_POST['logpass']);
    require('connection.php');
    if (@mysqli_fetch_row(mysqli_query($con, "SELECT * FROM 用户 WHERE 用户名='$logname' AND 密码='$logpass'")))
    {
        session_start();
        $_SESSION['id'] = $logname;
        session_write_close();
        setcookie('logname', $logname, time() + 2592000);
        setcookie('logpass', $logpass, time() + 2592000);
        header("Refresh:0,Url=home.php");
    }
    else
    {
        echo '<script>alert("用户名或密码错误")</script>';
        echo '<script type="text/javascript">window.location.replace("login.html");</script>';
    }
}
exit();
?>