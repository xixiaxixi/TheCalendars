<?php
require("../connection.php");
session_start();
$result = mysqli_query($con, "SELECT 姓名,学号,校园vpn密码,教务处密码 from 用户 WHERE 用户名='{$_SESSION['id']}' LIMIT 1");
echo json_encode(@mysqli_fetch_row($result));
session_write_close();
exit();