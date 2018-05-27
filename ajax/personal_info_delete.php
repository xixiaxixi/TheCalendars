<?php
require("../connection.php");
session_start();
mysqli_query($con, "UPDATE 用户 SET 姓名=null,学号=null,校园vpn密码=null,教务处密码=null WHERE 用户名='{$_SESSION['id']}'");
mysqli_query($con, "DELETE FROM 用户_课程 WHERE 用户名='{$_SESSION['id']}'");
session_write_close();
exit();
