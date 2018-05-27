<?php
/**
 * Created by PhpStorm.
 * User: 希希
 * Date: 2018/5/3
 * Time: 20:09
 */

header('Content-type: text/json');

if (isset($_POST['username'])) {
    require('connect_db.php');
    //main

    //get richen
$sql_prepare =<<<SQL
SELECT
  任务名,
  任务描述,
  截止时间
FROM 任务
  RIGHT JOIN (SELECT 工程代码
              FROM CalendarsDB.工程
              WHERE 创建者 = 'default' AND 工程名 = ?) AS T ON 任务.所属工程代码 = T.工程代码
WHERE 截止时间 > NOW()
ORDER BY 截止时间 ASC ;
SQL;
    $stmt = $con->prepare($sql_prepare);
    $stmt->bind_param('s', $_POST['username']);
    if (!$stmt->execute()) {
        echo '{"state": "fucking server"}';
        die(500);
    }else{
        if ($result = $stmt->get_result()) {
            if ($rows = mysqli_fetch_all($result)) {
                echo json_encode(array('state'=>'sc', 'richen'=>$rows));
            } else {
                echo '{"state" :"noRecord"}';
            }
        } else {
            echo '{"state": "fucking server"}';
            die(500);
        }
    }

} else {
    echo '{"state": "para error"}';
    die(400);
}