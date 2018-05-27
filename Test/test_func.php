<?php
require_once "../connection.php";
function fun()
{
    $redis->set('test',1);
    return $redis->get('test');
}