<?php
function getProjsData($user_id,$con,$redis)//输入用户id输出所有工程信息
{
    $projs=array();
    $proj_result = mysqli_query($con, "SELECT DISTINCT 所属工程代码 FROM 用户_任务 WHERE 执行者='$user_id'ORDER BY 所属工程代码 DESC");
    while ($proj_row = mysqli_fetch_array($proj_result))
        $projs[$proj_row['所属工程代码']]=getProjData($proj_row['所属工程代码'],$con,$redis);
    return $projs;
}

function getProjData($proj_id,$con,$redis)
{
    if ($redis->exists('projectdata'.$proj_id))//如果有redis就读redis
        return json_decode($redis->get('projectdata'.$proj_id),true);
    $data=getProjData_FromSESSION($proj_id,$con);
    $redis->set('projectdata'.$proj_id,json_encode($data));
    return $data;//如果没有redis，就从session读并且写进redis
}

function getProjData_FromSESSION($proj_id,$con)
{
    if(isset($_SESSION['projects'][$proj_id])&&!empty($_SESSION['projects'][$proj_id]))
        return $_SESSION['projects'][$proj_id];//如果有session就读session
    else
    {
        $data=getProjData_FromDB($proj_id,$con);
        $_SESSION['projects'][$proj_id]=$data;
        return $data;//没有session就从数据库里读并且写session
    }
}


function updateProjData($proj_id,$con,$redis)
{
    $data=getProjData_FromDB($proj_id,$con);
    $_SESSION['projects'][$proj_id] = $data;//更新session
    $redis->set('projectdata'.$proj_id,json_encode($data));//更新redis
}

function getProjData_FromDB($proj_id,$con)//查询工程信息
{
    $proj=array();
    $proj['id'] = $proj_id;//冗余

    $proj_result = mysqli_query($con, "SELECT * FROM 工程 WHERE 工程代码=$proj_id LIMIT 1");
    $proj_row=mysqli_fetch_array($proj_result);
    $proj['name']=$proj_row['工程名'];
    $proj['description']=$proj_row['工程描述'];
    $proj['maker']=$proj_row['创建者'];

    $proj['tasks'] = array();//array(任务代码=>任务详细信息)
    $proj['participants'] = array();//array(参与者Id=>参与者姓名)

    $task_result = mysqli_query($con, "SELECT * FROM 任务 WHERE 所属工程代码=$proj_id ORDER BY 截止时间 DESC");
    while ($task_row = mysqli_fetch_array($task_result))
    {
        if ($task_row['创建者'] != 'default')//如果不是默认任务
        {
            $task=array();
            $task['id'] = $task_row['任务代码'];//冗余
            $task['name'] = $task_row['任务名'];
            $task['description'] = $task_row['任务描述'];
            $task['maker'] = $task_row['创建者'];
            $task['deadline'] = $task_row['截止时间'];

            $task['participants'] = array();//找这个任务的所有参与者
            $participant_result = mysqli_query($con, "SELECT 执行者 FROM 用户_任务 WHERE 任务代码={$task_row['任务代码']}");
            while ($participant_row = mysqli_fetch_array($participant_result))
            {
                $name_result = mysqli_query($con, "SELECT 姓名 FROM 用户 WHERE 用户名='{$participant_row['执行者']}' LIMIT 1");
                $task['participants'][$participant_row['执行者']] = mysqli_fetch_array($name_result)['姓名'];
            }//$task['participants']=array('ydh'=>'尹达恒','lzq'=>'刘子迁');

            $task['formers'] = array();//找到这个任务的所有前继
            $former_result = mysqli_query($con, "SELECT 前任务代码 FROM 流程 WHERE 后任务代码={$task_row['任务代码']}");
            while ($former_row = mysqli_fetch_array($former_result))
            {
                $name_result = mysqli_query($con, "SELECT 任务名 FROM 任务 WHERE 任务代码={$former_row['前任务代码']} LIMIT 1");
                $task['formers'][$former_row['前任务代码']] = mysqli_fetch_array($name_result)['任务名'];
            }//$task['formers']=array(2=>'task2',3=>'task3');
            $proj['tasks'][$task_row['任务代码']] = $task;
        }
        else
        {
            $participant_result = mysqli_query($con, "SELECT 执行者 FROM 用户_任务 WHERE 任务代码={$task_row['任务代码']}");
            while ($participant_row = mysqli_fetch_array($participant_result))
            {
                $name_result = mysqli_query($con, "SELECT 姓名 FROM 用户 WHERE 用户名='{$participant_row['执行者']}' LIMIT 1");
                $proj['participants'][$participant_row['执行者']] = mysqli_fetch_array($name_result)['姓名'];
            }//$proj['participants']=array('ydh'=>'尹达恒','lzq'=>'刘子迁');
        }
    }
    return $proj;
}
