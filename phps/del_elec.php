<?php
/**
 * Created by PhpStorm.
 * User: 希希
 * Date: 2018/5/9
 * Time: 22:45
 */

header('Content-type: text/json');

if (isset($_POST['username'])&&isset($_POST['coursecode'])&&isset($_POST['courseno'])) {
    require('connect_db.php');
    //main
$sql_prepare_del = <<<SQL
DELETE FROM 用户_课程
WHERE 用户名= ? AND 课程代码 = ? AND 课程编号 = ?;
SQL;

    $stmt = $con->prepare($sql_prepare_del);
    $stmt->bind_param('sss', $_POST['username'],$_POST['coursecode'], $_POST['courseno']);
    if (!$stmt->execute()) {
        echo '{"state": "fucking server"}';
        die(500);
    }else{
        if ($stmt->affected_rows <= 0) {
            echo '{"state": "error", "detail": "fuck, I don\'t know why"}';
            die(400);
        } else {
            echo '{"state": "sc"}';
        }
    }
} else {
    echo '{"state": "para error", "detail": "username,coursecode,courseno needed"}';
    die(400);
}