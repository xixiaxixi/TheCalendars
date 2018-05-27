<?php
session_start();
if (!isset($_SESSION['id']))
{
    echo '<script type="text/javascript">window.location.replace("../login.html");</script>';
    exit();
}
$user_id = $_SESSION['id'];
session_write_close();
?>

<?php
if (!isset($_GET['proj_id']))
{
    echo '<script type="text/javascript">alert("传参失败");</script>';
    echo '<script type="text/javascript">window.location.replace("../home.php");</script>';
    exit();
}
else
{
    $proj_id = $_GET['proj_id'];
}
?>

<?php
require "../connection.php";
require "../functions/getProjsData.php";
$proj = getProjData($proj_id, $con, $redis);
$page_title = $proj['name'];
$css = '<link rel="stylesheet" type="text/css" href="css/TaskDiv.css"/>';
$script = '<script src="../lib/jquery1.8.3.min.js"></script>';
$script = $script . '<script src="../lib/jquery.rotate-0.3.0/jquery.rotate.js"></script>';
$script = $script . '<script src="js/ArrowBoxFunctions.js"></script>';
$script = $script . '<script src="js/Renderer.js"></script>';
$script = $script . '<script src="js/ProjectPageVars&Funs.js"></script>';
$script = $script . '<script src="js/ProjectPageUpdateFuns.js"></script>';

require_once('header.html');
?>
    <button class="toggle-flow-pic" id="toggle-flow-pic">
        <img src="../img/process-o.svg" class="add-icon">
    </button>
    <div class="main-pic-container">
        <canvas class="main-pic" id='<?php echo "$proj_id" ?>'>Your browser doesn't support canvas.</canvas>
    </div>
    <div class="swiper-container" id="swiper-container">
        <div class="swiper-wrapper" id="swiper-wrapper">
            <div class="swiper-slide">
                <div class="head-part">
                    <button class="opera-button show-add-div" id="show-add-div">New</button>
                </div>
                <?php
                require "functions/TaskDiv.php";
                add_task_div($proj);
                foreach ($proj['tasks'] as $task_id => $task_data)
                    if ($task_data['maker'] == $user_id)
                        task_div($proj, $task_data);
                    else
                        viewonly_task_div($proj, $task_data);
                ?>
            </div>
        </div>
    </div>


<?php
$script = '<script src="js/ProjectPageInit.js"></script>';
require_once('footer.html');
?>