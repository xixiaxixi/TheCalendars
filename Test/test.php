<?php
require "../functions/getProjsData.php";
require "../connection.php";
$user_id='yin';
$projs=getProjsData($user_id, $con);
foreach ($projs as $proj_id=>$proj)
{
    echo"$proj_id=>";
    echo"{$proj['id']}\n";
    echo"{$proj['name']}\n";
    echo"{$proj['maker']}\n";
    foreach ($proj["tasks"] as $id => $task)
        echo "$id ";
    foreach ($proj["tasks"] as $id => $task)
    {
        echo "$id:";
        echo $task['name'];
        echo ' ';
        echo $task['description'];
        echo ' ';
        echo $task['maker'];
        echo ' ';
        echo $task['deadline'];
        echo ' ';
        foreach ($task['formers'] as $task_id => $task_name)
        {
            echo "$task_id ";
            echo"$task_name";
        }

        echo "<br>";
    }
}
echo"<br>";
echo json_encode($projs);




