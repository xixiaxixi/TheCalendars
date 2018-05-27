<?php
/**
 * Created by PhpStorm.
 * User: 希希
 * Date: 2018/5/10
 * Time: 14:53
 */

header('Content-type: text/json');

if (isset($_POST['username'])&&isset($_POST['zfname'])&&isset($_POST['zfpswd'])) {

    $redis = new Redis();
    if ($redis->connect('120.79.175.123', 6379)) {
        $redis->auth('cc$3377');
        try {
            $state = $redis->hGet($_POST['username'], 'state');
            if ($state == "ld") {
                echo '{"state":"ld"}';
            } else {
                if (empty($_POST['year'])&&empty($_POST['term'])) {
                    $redis->hMset($_POST['username'], Array('zfname'=>$_POST['zfname'], 'zfpswd'=>$_POST['zfpswd'] ));
                    $redis->hDel($_POST['username'], 'year', 'term');
                } elseif (empty($_POST['year']) && !empty($_POST['term'])) {
                    $redis->hMset($_POST['username'], Array(
                        'zfname'=>$_POST['zfname'],
                        'zfpswd'=>$_POST['zfpswd'],
                        'term'=>$_POST['term']
                    ));
                    $redis->hDel($_POST['username'], 'year');
                } elseif (!empty($_POST['year']) && empty($_POST['term'])) {
                    $redis->hMset($_POST['username'], Array(
                        'zfname'=>$_POST['zfname'],
                        'zfpswd'=>$_POST['zfpswd'],
                        'year'=>$_POST['year']
                    ));
                    $redis->hDel($_POST['username'], 'term');
                } elseif (!empty($_POST['year']) && !empty($_POST['term'])) {
                    $redis->hMset($_POST['username'], Array(
                        'zfname'=>$_POST['zfname'],
                        'zfpswd'=>$_POST['zfpswd'],
                        'year' => $_POST['year'],
                        'term' => $_POST['term']
                    ));
                }
                $redis->lPush('queue', $_POST['username']);
                $redis->lPush($_POST['username'], 'ld');
                $queue_length = $redis->lLen('queue');
                echo '{"state":"ok","queue":'.$queue_length.'}';
            }
        } catch (RedisException $exception) {
            echo '{"state":"ce"}';
            die(500);
        }
    } else {
        echo '{"state": "shit"}';
        die(500);
    }
} else {
    echo '{"state": "para error". "detail": "username,zfname,zfpswd needed"}';
    die(400);
}
