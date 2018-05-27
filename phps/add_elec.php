<?php
/**
 * Created by PhpStorm.
 * User: 希希
 * Date: 2018/5/10
 * Time: 10:49
 */

header('Content-type: text/json');

if (isset($_POST['username'])&&isset($_POST['coursecode'])&&isset($_POST['courseno'])) {
    require('connect_db.php');
    //main
$sql_prepare_add = <<<SQL
INSERT INTO 用户_课程
VALUES (?, ?, ?, '是');
SQL;

    $stmt = $con->prepare($sql_prepare_add);
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
