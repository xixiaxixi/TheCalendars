<?php
session_start();
$path=$_SESSION['canvas_json_path'];
session_write_close();
$proj_id = $_GET['proj_id'];
require_once "../connection.php";
require_once "../ajax/TaskMultiple/create_json.inc.php";
if ($redis->exists('canvasdata' . $proj_id))
    echo $redis->get('canvasdata' . $proj_id);
elseif (is_file('../' . $path . $proj_id . '.json'))
{
    $data = fread(fopen('../' . $path . $proj_id . '.json', 'r'), filesize('../' . $path . $proj_id . '.json'));
    echo $data;
    $redis->set('canvasdata' . $proj_id,$data);
}
else
    echo create_json_by_projid($proj_id, $dbc, $redis, 'error_func', '../' . $path);
exit();