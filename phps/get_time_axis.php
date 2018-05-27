<?php
/**
 * Created by PhpStorm.
 * User: 希希
 * Date: 2018/5/5
 * Time: 15:50
 */

header('Content-type: text/json');

if (isset($_POST['username'])) {
    require('connect_db.php');
    //main
$sql_prepare = <<<SQL
(SELECT
   任务名,
   任务描述,
   截止时间,
   'pro' AS fr,
   所属工程代码
 FROM 任务
   RIGHT JOIN (SELECT 任务代码
               FROM CalendarsDB.用户_任务
               WHERE 执行者 = ?) AS T USING (任务代码) # username
 WHERE 截止时间 > NOW())
UNION
(SELECT
   任务名,
   任务描述,
   截止时间,
   'ric' AS fr,
   'null' AS 所属工程代码
 FROM 任务
   RIGHT JOIN (SELECT 工程代码
               FROM CalendarsDB.工程
               WHERE 创建者 = 'default' AND 工程名 = ?) AS T ON 任务.所属工程代码 = T.工程代码
 WHERE 截止时间 > NOW())
ORDER BY 截止时间 ASC;
SQL;
    $stmt = $con->prepare($sql_prepare);
    $stmt->bind_param('ss', $_POST['username'], $_POST['username']);
    if (!$stmt->execute()) {
        echo '{"state": "fucking server"}';
        die(500);
    } else {
        if ($result = $stmt->get_result()) {
            if ($rows = mysqli_fetch_all($result)) {
                echo json_encode(array('state'=>'sc', 'event'=>$rows));
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
