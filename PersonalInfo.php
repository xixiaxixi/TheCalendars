<?php
echo "<script src='js/PersonalInfoCheck.js'></script>";
session_start();
if (!isset($_SESSION['id']))
{
    header("Location:login.html");
    exit();
}
$user_id=$_SESSION['id'];
session_write_close();

require('connection.php');
if (!isset($_POST['Name']) or empty($_POST['Name']))
{
    echo '<script>alert("请输入姓名")</script>';
    echo '<script type="text/javascript">window.location.replace("PersonalInfo.html");</script>';
}
elseif (!isset($_POST['StudentID']) or empty($_POST['StudentID']))
{
    echo '<script>alert("请输入学号")</script>';
    echo '<script type="text/javascript">window.location.replace("PersonalInfo.html");</script>';
}
elseif (!isset($_POST['VPNPassword']) or empty($_POST['VPNPassword']))
{
    echo '<script>alert("请输入校园VPN密码")</script>';
    echo '<script type="text/javascript">window.location.replace("PersonalInfo.html");</script>';
}
elseif (!isset($_POST['RegistryDepartmentPassword']) or empty($_POST['RegistryDepartmentPassword']))
{
    echo '<script>alert("请输入教务处密码")</script>';
    echo '<script type="text/javascript">window.location.replace("PersonalInfo.html");</script>';
}
else
{
    $Name = $_POST['Name'];
    $StudentID = $_POST['StudentID'];
    $VPNPassword = $_POST['VPNPassword'];
    $RegistryDepartmentPassword = $_POST['RegistryDepartmentPassword'];
    $SQL = "UPDATE 用户 SET 姓名='$Name',学号='$StudentID',校园vpn密码='$VPNPassword',教务处密码='$RegistryDepartmentPassword'WHERE 用户名='{$user_id}'";
    if (mysqli_query($con, $SQL))
    {
        echo '<script>alert("输入成功！")</script>';
        echo "<script>course_data_import($user_id,$StudentID,$VPNPassword,$RegistryDepartmentPassword)</script>";
        //require_once "functions/load_courses.php";
        //LoadCourses($user_id, $StudentID, $RegistryDepartmentPassword);
        header("Refresh:0,Url=home.php");
    }
    else
    {
        echo '<script>alert("输入失败，请检查网络连接")</script>';
        echo '<script type="text/javascript">window.location.replace("PersonalInfo.html");</script>';
    }
}
exit();