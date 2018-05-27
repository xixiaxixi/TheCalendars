<?php
$con = mysqli_connect('localhost:3306', 'CalendarsDB', 'CalendarsDB', 'CalendarsDB');
$dbc = mysqli_connect('localhost:3306', 'CalendarsDB', 'CalendarsDB', 'CalendarsDB');
if ($con->connect_errno)
{
    die("连接失败: (" . $con->connect_errno . ") " . $con->connect_error);
}
if ($dbc->connect_errno)
{
    die("连接失败: (" . $con->connect_errno . ") " . $con->connect_error);
}
$redis=new Redis();
$redis->connect('127.0.0.1',6379);
?>