<?php
require_once "lib/Mobile-Detect-2.8.31/Mobile_Detect.php";
$detect = new Mobile_Detect;
setcookie('isMobile', $detect->isMobile(), time() + 86400);
require('connection.php');
if (isset($_COOKIE['logname']) and isset($_COOKIE['logpass']))
{
    $logname = htmlspecialchars($_COOKIE['logname']);
    $logpass = $_COOKIE['logpass'];
    if (@mysqli_fetch_row(mysqli_query($con, "SELECT * FROM 用户 WHERE 用户名='$logname' AND 密码='$logpass'")))
    {
        session_start();
        $_SESSION['id'] = $logname;
        session_write_close();
        header("Refresh:0,Url=home.php");
        exit();
    }
}
echo '<script type="text/javascript">window.location.replace("login.html");</script>';
exit();