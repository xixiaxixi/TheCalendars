<?php
/**
 * Created by PhpStorm.
 * User: 希希
 * Date: 2018/5/11
 * Time: 21:12
 */

DEFINE('DB_USER', 'CalendarsDB');
DEFINE('DB_PASSWORD', 'CalendarsDB');
DEFINE('DB_HOST', '39.108.232.131:3306');
DEFINE('DB_NAME', 'CalendarsDB');
$con = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die('Could not
connect to MySQL:' . mysqli_connect_error());
//mysqli_set_charset($dbc, 'utf8');
