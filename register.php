<?php
require('connection.php');
if (!isset($_POST['logname']) or empty($_POST['logname']))
{
    echo '<script>alert("请输入用户名")</script>';
    echo '<script type="text/javascript">window.location.replace("register.html");</script>';
}
elseif (!isset($_POST['logpass']) or empty($_POST['logpass']))
{
    echo '<script>alert("请输入密码")</script>';
    echo '<script type="text/javascript">window.location.replace("register.html");</script>';
}
elseif ($_POST['logpass'] !== $_POST['confirmlogpass'])
{
    echo '<script>alert("密码不一致,请重新输入")</script>';
    echo '<script type="text/javascript">window.location.replace("register.html");</script>';
}
elseif (mb_strlen($_POST['logname']) <= 6)
{
    echo '<script>alert("用户名应大于6个字符,请重新输入")</script>';
    echo '<script type="text/javascript">window.location.replace("register.html");</script>';
}
elseif (!preg_match("/^[a-z\d]*$/i",$_POST['logname']))
{
    echo '<script>alert("用户名只能是数字和字母的组合,请重新输入")</script>';
    echo '<script type="text/javascript">window.location.replace("register.html");</script>';
}
else
{
    $logname = htmlspecialchars($_POST['logname']);
    if (@mysqli_fetch_row(mysqli_query($con, "SELECT * FROM 用户 WHERE 用户名='$logname'")))
    {
        echo "<script>alert('用户名 $logname 已存在,请使用其他用户名')</script>";
        echo '<script type="text/javascript">window.location.replace("register.html");</script>';
    }
    else
    {
        $logpass = md5($_POST['logpass']);
        if (mysqli_query($con, "INSERT INTO 用户(用户名,密码) VALUES ('$logname','$logpass')"))
        {
            echo '<script>alert("注册成功！")</script>';
            session_start();
            $_SESSION['id'] = $logname;
            session_write_close();
            setcookie('logname', $logname, time() + 2592000);
            setcookie('logpass', $logpass, time() + 2592000);
            header("Refresh:0,Url=home.php");
        }
    }
}
exit();
?>