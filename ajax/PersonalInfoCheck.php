<?php
require("../connection.php");
$result = mysqli_query($con, "SELECT 学号,校园vpn密码,教务处密码 from 用户 WHERE 用户名='{$_POST['logname']}' LIMIT 1");
echo json_encode(@mysqli_fetch_row($result));
exit();
?>