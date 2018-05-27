<?php
require("../connection.php");
if($_POST['search_id']=='default')
{
    echo "error:查无此人";
    exit();
}
$q="SELECT * FROM 用户 WHERE 用户名='{$_POST['search_id']}'";
$result=mysqli_query($con, $q);
if(!$result)
{
    echo "error:".$q;
    exit();
}
$row=@mysqli_fetch_array($result);
if($row)
{
    echo (isset($row['姓名'])and!empty($row['姓名']))?$row['姓名']:$_POST['search_id'];
    exit();
}
else
{
    echo "error:查无此人";
    exit();
}

?>