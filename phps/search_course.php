<?php
/**
 * Created by PhpStorm.
 * User: 希希
 * Date: 2018/5/9
 * Time: 21:44
 */

header('Content-type: text/json');

if (isset($_POST['username'])&&isset($_POST['word'])) {
    require('connect_db.php');
    //main
$sql_prepare = <<<SQL
SELECT 课程.课程代码,课程.课程编号,课程名称,教师,教室,周上课日,起始节数,持续节数,开始周,结束周,单双周,课.是否蹭课
FROM 课程
  LEFT JOIN 用户_课程 课 ON 课程.课程代码 = 课.课程代码 AND 课程.课程编号 = 课.课程编号 AND 用户名=?
WHERE (课程名称 LIKE ? OR 教师 LIKE ? OR 教室 LIKE ?)
LIMIT 100;
SQL;

    $stmt = $con->prepare($sql_prepare);
    $word = '%' . $_POST['word'] . '%';
    $stmt->bind_param('ssss', $_POST['username'],$word,$word,$word);
    if (!$stmt->execute()) {
        echo '{"state": "fucking server"}';
        die(500);
    }else{
        if ($result = $stmt->get_result()) {
            if ($rows = $result->fetch_all(MYSQLI_ASSOC)) {
                echo json_encode(array('state'=>'sc', "result"=>$rows));
            } else {
                echo '{"state": "noResult", "detail": "please changeword"}';
                die(500);
            }
        } else {
            echo '{"state": "fucking server"}';
            die(500);
        }
    }
} else {
    echo '{"state": "para error", "detail": "username,word needed"}';
    die(400);
}