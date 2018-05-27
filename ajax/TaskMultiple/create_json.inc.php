<?php
function error_func($q)
{
    echo "error:$q";
    exit();
}

function create_json_by_username($id, $dbc,$redis, $error_func, $path)
{
    $q = "SELECT DISTINCT 所属工程代码 FROM 用户_任务 WHERE 执行者 = '$id'";
    $r = mysqli_query($dbc, $q);
    if (!$r)
    {
        $error_func($q);
        exit();
    }
    date_default_timezone_set('Asia/Shanghai');

    while ($row = mysqli_fetch_array($r))
    {
        if(!$redis->exists('canvasdata'.$row[0])||!is_file($path . $row[0])||!create_json_by_projid($row[0], $dbc,$redis, $error_func, $path))
            return false;
    }
    return true;
}

function create_json_by_projid($proj_id, $dbc,$redis, $error_func, $path)
{
    $nodes = array();
    $edges = array();
    $q2 = "SELECT 任务代码, 任务名, 截止时间 FROM 任务 WHERE 所属工程代码='$proj_id' AND 创建者 != 'default'";
    $r2 = mysqli_query($dbc, $q2);
    if (!$r2)
    {
        $error_func($q2);
        exit();
    }
    while ($row2 = mysqli_fetch_array($r2))
    {
        $deadline = strtotime($row2['截止时间']) - time();
        ${$row2['任务代码']} = array("title" => $row2['任务名']);

        if ($deadline < 0)
        {
            ${$row2['任务代码']}['color'] = "rgba(255,0,0,0.333)";
            ${$row2['任务代码']}['deadline'] = '已完成';
        }
        else
        {
            ${$row2['任务代码']}['color'] = "rgba(0,255,0,0.333)";
            $hrs = ceil(($deadline) / 3600);
            $days = floor(($hrs) / 24);
            $hrs = $hrs - $days * 24;
            if ($days > 0)
            {
                ${$row2['任务代码']}['deadline'] = $days . '天';
            }
            else
            {
                ${$row2['任务代码']}['deadline'] = $hrs . '小时';
            }

            //查前任务
            $q4 = "SELECT 前任务代码 FROM 流程 WHERE 后任务代码='{$row2['任务代码']}'";
            $r4 = mysqli_query($dbc, $q4);
            if (!$r4)
            {
                $error_func($q4);
                exit();
            }
            while ($row4 = mysqli_fetch_array($r4))
            {
                $q5 = "SELECT 截止时间 FROM 任务 WHERE 任务代码='$row4[0]' LIMIT 1";
                $r5 = mysqli_query($dbc, $q5);
                if (!$r5)
                {
                    $error_func($q5);
                    exit();
                }
                $row5 = mysqli_fetch_array($r5);
                if ($row5 and ((strtotime($row5['截止时间']) - time()) > 0))
                {
                    ${$row2['任务代码']}['color'] = "rgba(0,0,255,0.333)";
                    ${$row2['任务代码']}['deadline'] = '未开始';
                    break;
                }
            }
        }
        //${$row2['任务代码']}['deadline']=$deadline;
        $nodes[$row2['任务代码']] = ${$row2['任务代码']};
        $q3 = "SELECT 后任务代码 FROM 流程 WHERE 前任务代码 = '{$row2['任务代码']}'";
        $r3 = mysqli_query($dbc, $q3);
        if (!$r3)
        {
            $error_func($q3);
            exit();
        }
        while ($row3 = mysqli_fetch_array($r3))
        {
            $edges[$row2['任务代码']][$row3[0]] = (object)array();
        }
    }
    $filename = $path . $proj_id;
    $data = array(
        'nodes' => $nodes,
        'edges' => $edges
    );
    $json_str = json_encode($data);
    $res = file_put_contents($filename . '.json', $json_str);
    if ($res == false)
    {
        $error_func('文件写入失败');
        exit();
    }
    $redis->set('canvasdata'.$proj_id,$json_str);
    return $json_str;
}
?>
