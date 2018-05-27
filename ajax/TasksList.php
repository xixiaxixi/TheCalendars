<?php
$proj_id=$_GET['proj_id'];
require_once "../connection.php";
require_once "../functions/getProjsData.php";
$projdata=getProjData($proj_id,$con,$redis);
$projdata=array_keys($projdata['tasks']);
sort($projdata);
echo json_encode($projdata);
exit();