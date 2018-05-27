<?php
/**
 * Created by PhpStorm.
 * User: 希希
 * Date: 2018/5/8
 * Time: 0:18
 */

header('Content-type: text/json');

if (isset($_POST['username'])) {
    require('connect_db.php');
    //main

    //get richen
$sql_prepare =<<<SQL
SELECT *
FROM 课程
  RIGHT JOIN
  (SELECT *
   FROM 用户_课程
   WHERE 用户名 = ?) AS T ON (T.课程代码 = 课程.课程代码 AND T.课程编号 = 课程.课程编号)
ORDER BY 起始节数, 周上课日 ASC;
SQL;

    $stmt = $con->prepare($sql_prepare);
    $username = $_POST['username'];
    settype($username, "string");
    $stmt->bind_param('s', $username);
    if (!$stmt->execute()) {
        echo '{"state": "fucking server"}';
        die(500);
    }else{
        if ($result = $stmt->get_result()) {
            if ($rows = mysqli_fetch_all($result,MYSQLI_ASSOC)){
                echo json_encode(array('state'=>'sc', "table"=>$rows));
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
