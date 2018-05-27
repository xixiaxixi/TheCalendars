<?php
require("../connection.php");
echo(((bool)@mysqli_fetch_row(mysqli_query($con, "SELECT * FROM 用户 WHERE 用户名='{$_POST['logname']}'")))
    or (mb_strlen($_POST['logname']) > 10));
exit();
?>