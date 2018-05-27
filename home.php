<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['id']))
{
    header("Location:login.html");
    exit();
}
$user_id=$_SESSION['id'];
$_SESSION['canvas_json_path'] = "jsons/";
$path=$_SESSION['canvas_json_path'];
session_write_close();
require_once "connection.php";
require_once "functions/getProjsData.php";
$projs = getProjsData($user_id, $con,$redis);//获取工程数据
require_once "ajax/TaskMultiple/create_json.inc.php";
create_json_by_username($user_id, $dbc,$redis, 'error_func', $path);
?>

<html lang="en" class="no-js">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <title>TheCalendars</title>
    <link rel="stylesheet" type="text/css" href="css/normalize.css"/>
    <link rel="stylesheet" href="lib/swiper-4.2.2/dist/css/swiper.min.css">
    <!--必要样式-->
    <link rel="stylesheet" type="text/css" href="css/HomePage.css"/>
    <!--[if IE]>
    <script src="lib/html5.js"></script>
    <![endif]-->

    <link rel="stylesheet" type="text/css" href="css/block.css"/>
    <link rel="stylesheet" type="text/css" href="css/loading_ui.css"/>
    <script src="js/jquery-3.3.1.js"></script>
    <script src="js/home_event.js"></script>
    <script src="js/home_autoload.js"></script>

</head>
<body>
<div class="container">
    <div id="MainPage" class="MainPage">
        <canvas id="BackGroundAnimation"></canvas>
        <div id='main_box' class="main_box">
            <!-- Swiper -->
            <div class="swiper-container" id="swiper-container">
                <div class="swiper-wrapper" id="swiper-wrapper">
                    <div class="main-swiper swiper-slide">
                        <div class="sub-swiper-container">
                            <div class="swiper-wrapper">
                                <div class="sub-swiper swiper-slide">
                                    <div id="event_axis">
                                        <h1>日程表</h1>
                                        <div class="hor_menu">
                                            <div class="hor_menu_item" onclick="rev_i_hidden()">添加日程</div>
                                        </div>
                                        <div id="input_add_richen">
                                            <div class="input_row">
                                                截止时间
                                                <input type="datetime-local" class='input-type'/>
                                            </div>
                                            <div class="input_row">
                                                日程名称
                                                <textarea class='input-type' style="margin-top: 5px;padding-left: 0px;padding-right: 1px;" rows="1"></textarea>
                                                <!--                                                <input class="text_area"  type="text" placeholder="	Couse Name" >-->

                                            </div>
                                            <div class="input_row">详细备注
                                                <textarea class='input-type' rows="2" style="padding-left: 0px;padding-right: 1px;"></textarea>
                                            </div>
                                            <div id="btn_insert_richen" onclick="insert_richen()">提交</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="main-swiper swiper-slide">
                        <div class="sub-swiper-container">
                            <div class="swiper-wrapper">
                                <div class="sub-swiper swiper-slide">
                                    <h1>课程表</h1>
                                    <div class="hor_menu">
                                        <div class="hor_menu_item i33" onclick="rev_a_hidden()" style="margin-left:0;">载入课程</div>
                                        <div class="hor_menu_item i33" style="margin-right:0;"
                                             onclick="window.open('change_course.php','_self')">添加课程
                                        </div>
                                    </div>
                                    <div id="input_load_course">
                                        <div class="input_row">
                                            <span style="vertical-align: top">教务处账号</span>
                                            <input id="ipt_zf_acc">
                                        </div>
                                        <div class="input_row">
                                            <span style="vertical-align: top">教务处密码</span>
                                            <input type="password" id="ipt_zf_pswd">
                                            <!--                                            <textarea rows="1"></textarea>-->
                                        </div>
                                        <div id="btn_load_course" onclick="add_course()">获取</div>
                                    </div>
                                    <table class="coursetable" border="1" align="center">
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="main-swiper swiper-slide">
                        <div class="sub-swiper-container">
                            <div class="swiper-wrapper">
                                <div class="sub-swiper swiper-slide">
                                    <h1>工程表</h1>
                                    <div class="hor_menu">
                                        <div class="hor_menu_item"
                                             onclick="window.open('Task/new_group.php?action=new')">新建工程
                                        </div>
                                    </div>
                                    <?php
                                    require_once "functions/ProjDiv.php";
                                    foreach ($projs as $proj_id => $proj_data) //显示
                                    {
                                        if($proj_data['maker']==$user_id)
                                            proj_div($proj_data);
                                        else
                                            viewonly_proj_div($proj_data);
                                    }


                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="main-swiper swiper-slide">
                        <div class="sub-swiper-container">
                            <div class="swiper-wrapper">
                                <div class="sub-swiper swiper-slide" style="font-size: 25px">
                                    <br><br>
                                    <a href="login.php?action=logout">Logout</a>
                                    <br><br>
                                    <a href="PersonalInfo.html">填写个人信息</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Add Pagination -->
            </div>
        </div>
    </div>
</div><!-- /container -->
<script src="lib/TweenLite.min.js"></script>
<script src="lib/EasePack.min.js"></script>
<script src="lib/rAF.js"></script>
<script src="lib/BackGroundAnimation.js"></script>
<!-- Swiper JS -->
<script src="lib/swiper-4.2.2/dist/js/swiper.min.js"></script>
<!-- Initialize Swiper -->
<script>
    var swiper = new Swiper('.swiper-container', {
        slidesPerView: 'auto',
        centeredSlides: true,
        spaceBetween: 30,
        initialSlide: 1
    });
    var sub_swiper1 = new Swiper('.sub-swiper-container', {
        direction: 'vertical',
        slidesPerView: 'auto',
        freeMode: true,
        scrollbar: {
            el: '.swiper-scrollbar'
        },
        mousewheel: true,
        observer: true,
        observeParents: true
    });
</script>
<script src="js/HomePage.js"></script>
</body>
</html>
