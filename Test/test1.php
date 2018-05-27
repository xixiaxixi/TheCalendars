<?php
//$redis=new Redis();
//$redis->connect('127.0.0.1',6379);
//echo $redis->ping();
//echo $redis->set('admin','yin');
//echo $redis->get('canvasdata9');
//echo "\n";

//$data=array('a'=>'a','b'=>'bb','c'=>'ccc');
//echo $data;
//echo json_encode($data);
//$redis->set('projectdata9',json_encode($data));
//$data=json_decode($redis->get('projectdata9'));
//$data=json_decode(json_encode($data),true);//错误?
//echo $data['b'];
require_once "../connection.php";
require_once "../functions/getProjsData.php";
$proj_id=$_POST['proj_id'];
echo json_encode(getProjData_FromDB($proj_id,$con));



