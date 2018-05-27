<?php
function ProjChangeDataPush($user_id, $proj_data, $task_id, $what_change, $redis)
{
    foreach ($proj_data['participants'] as $id => $name)
    {
        if ($id != $user_id)//当前登录用户的修改不入当前用户的消息队列
        {
            $key = $id . 'projchange' . $proj_data['id'];
            if ($what_change == 'finish')
                $value = json_encode(array('type' => $what_change, 'tid' => $task_id, 'deadline' => $proj_data['tasks'][$task_id]['deadline']));
            elseif ($what_change == 'alter')
                $value = json_encode(array('type' => $what_change, 'tid' => $task_id, 'newname' => $proj_data['tasks'][$task_id]['name']));
            elseif($what_change=='new')
                $value=json_encode(array('type' => $what_change, 'tid' => $task_id, 'name' => $proj_data['tasks'][$task_id]['name']));
            else
                $value = json_encode(array('type' => $what_change, 'tid' => $task_id));
            $redis->lPush($key, $value);//用户名+projchange+工程id=>修改情况
            $redis->expire($key, 60);
        }//更新用户工程修改的消息队列（从$data里面获取相关用户）
    }
}