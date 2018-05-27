<?php
/**
 * Created by PhpStorm.
 * User: 希希
 * Date: 2018/5/5
 * Time: 15:30
 */

header('Content-type: text/json');

if (isset($_POST['username'])) {
    require('connect_db.php');
    //main
    $sql_prepare = "SELECT 任务名,任务描述,截止时间 FROM 任务 RIGHT JOIN (SELECT 任务代码 FROM CalendarsDB.用户_任务 WHERE 执行者 = ?) AS T USING(任务代码) WHERE 截止时间 > NOW() ORDER BY 截止时间 ASC;";
    $stmt = $con->prepare($sql_prepare);
    $stmt->bind_param('s', $_POST['username']);
    if (!$stmt->execute()) {
        echo '{"state": "fucking server"}';
        die(500);
    } else {
        if ($result = $stmt->get_result()) {
            if ($rows = mysqli_fetch_all($result)) {
                echo json_encode(array('state'=>'sc', 'project'=>$rows));
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
