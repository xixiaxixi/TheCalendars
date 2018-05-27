<?php
/**
 * Created by PhpStorm.
 * User: 希希
 * Date: 2018/5/10
 * Time: 14:36
 */

/**
 * All zf states including:
 * em   empty, user has not used this yet
 * nue  user not exist
 * ue   zf account not exist
 * ke   zf password error
 * ye   year not exist in system
 * te   term not exist in system
 * ld   course table is loading, waiting please
 * xx   unexpected exception when executing script
 * And these phps also may return:
 * 400 error:
 *  para error
 * 500 error:
 *  shit
 *  fuck
 *  xx  cloud table analysis error
 *  ce  cloud db connect error
 */

header('Content-type: text/json');

if (isset($_POST['username'])) {
    $redis = new Redis();
    if ($redis->connect('120.79.175.123', 6379)) {
        $redis->auth('cc$3377');
        try {
            $state = $redis->hGet($_POST['username'], 'state');
            if ($state == "") {
                echo '{"state": "em"}';
            } else {
                if ($state == 'ld') {
                    $queue_length = $redis->lLen('queue');
                    echo '{"state":"' . $state . '","queue":'.$queue_length.'}';
                } else
                    echo '{"state":"' . $state . '"}';
            }
        } catch (RedisException $exception) {
            echo '{"state":"ce"}';
            die(500);
        }
    } else {
        echo '{"state": "ce"}';
        die(500);
    }
} else {
    echo '{"state": "para error", "detail": "username needed"}';
    die(400);
}

