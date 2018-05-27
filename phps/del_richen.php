<?php
/**
 * Created by PhpStorm.
 * User: 希希
 * Date: 2018/5/7
 * Time: 21:32
 */

header('Content-type: text/json');

if (isset($_POST['username'])&&isset($_POST['taskname'])&& isset($_POST['deadline'])) {
    require('connect_db.php');
    //main

    //get pro id
$sql_prepare_proid = <<<SQL
SELECT 工程代码
FROM 工程
WHERE 创建者 = 'default' AND 工程名 = ?
LIMIT 1;
SQL;
    //del richen
$sql_prepare_delete =<<<SQL
DELETE FROM 任务
WHERE 任务名 = ? AND 任务描述 = ? AND 截止时间 = ? AND 创建者='default' AND 所属工程代码 = ?
LIMIT 1;
SQL;

    $stmt = $con->prepare($sql_prepare_proid);
    $stmt->bind_param('s', $_POST['username']);
    if (!$stmt->execute()) {
        echo '{"state": "fucking server"}';
        die(500);
    }else{
        if ($result = $stmt->get_result()) {
            if ($row = $result->fetch_array()) {
                $pro_id = $row[0];

                //main_main
                $stmt = $con->prepare($sql_prepare_delete);
                $stmt->bind_param('ssss', $_POST['taskname'], $_POST['taskdetail'], $_POST['deadline'], $pro_id);
                if (!$stmt->execute()) {
                    echo '{"state": "fucking server"}';
                    die(500);
                }else{
                    if ($stmt->affected_rows <= 0) {
                        echo '{"state": "para error", "detail": "fuck, maybe datetime syntax error"}';
                        die(400);
                    } else {
                        echo '{"state": "sc"}';
                    }
                }
            } else {
                echo '{"state": "user not exist", "detail": "please contact admin"}';
                die(500);
            }
        } else {
            echo '{"state": "fucking server"}';
            die(500);
        }
    }
} else {
    echo '{"state": "para error", "detail": "username,taskname,deadline needed"}';
    die(400);
}