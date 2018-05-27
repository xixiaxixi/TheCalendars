<?php
/**
 * Created by PhpStorm.
 * User: 希希
 * Date: 2018/5/11
 * Time: 22:21
 */

session_start();
if (!isset($_SESSION['id']))
{
    header("Location:login.html");
    exit();
}
require_once "connection.php";
require_once "functions/getProjsData.php";
$data=getProjsData($_SESSION['id'],$con,$redis);//将所有工程数据写入session和json
require_once "ajax/TaskMultiple/create_json.inc.php";
$_SESSION['canvas_json_path']="jsons/";
create_json_by_username($_SESSION['id'], $dbc,$redis, 'error_func', $_SESSION['canvas_json_path']);
session_write_close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Course</title>
    <link rel="stylesheet" type="text/css" href="css/add_course_2.css"/>

    <link rel="stylesheet" href="lib/swiper-4.2.2/dist/css/swiper.min.css">
    <script src="lib/swiper-4.2.2/dist/js/swiper.min.js"></script>


    <script src="js/jquery-3.3.1.js"></script>
</head>
<body>
<div class="flex outer">
    <div class="flex item overflow"> <!-- 嵌套的item加flex样式 及 overflow: auto属性 -->
        <div class="flex contener overflow">                  <!-- overflow: auto 高度自适应必须 -->
            <div id="search_wrapper">
                <div class="Seacher">
                    <div class="div_ipt">
                        <input class="text_area"  type="text" placeholder="Course Name" >
                    </div>
                    <div class="div_search">
                        <a>
                            <button class="search" onclick="btn_search_click()">搜索</button>
                        </a>
                    </div>
                    <div class="div_combo">
                        <div class="disp_tag" id="t" onclick="rev_display('t')">教师</div>
                        <div class="disp_tag" id="cr" onclick="rev_display('cr')">教室</div>
                        <div class="disp_tag" id="day" onclick="rev_display('day')">周上课日</div>
                        <div class="disp_tag" id="pr" onclick="rev_display('pr')">节数</div>
                        <div class="disp_tag" id="wk" onclick="rev_display('wk')">周</div>
                        <div class="disp_tag" id="isc" onclick="rev_display('isc')">是否自选</div>
                    </div>
                </div>
            </div>
            <div class="item overflow_inner">                         <!-- overflow: auto 高度自适应必须 -->
                <div class="monitor">
                    <div class="inner">

<!--                        <div class="swiper-container" id="swiper-container">-->
<!--                            <div class="swiper-wrapper" id="swiper-wrapper">-->
<!--                                <div class="main-swiper swiper-slide">-->

                        <table class="result" align="center">
                            <tbody></tbody>
                        </table>

<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->

                    </div>
                </div>
            </div>
            <div id="btn_ret_wrapper">
                <div id="btn_ret" onclick="javascript:history.back();">完成</div>
            </div>
        </div>
    </div>
</div>
<!--<script>-->
<!--    console.log('shit');-->
<!--    var swiper = new Swiper('.swiper-container', {-->
<!--        direction: 'vertical',-->
<!--        slidesPerView: 'auto',-->
<!--        freeMode: true,-->
<!--        centeredSlides: true,-->
<!--        scrollbar: {-->
<!--            el: '.swiper-scrollbar'-->
<!--        },-->
<!--        mousewheel: true,-->
<!--        observer: true,-->
<!--        observeParents: true-->
<!--    });-->
<!--</script>-->
<script src="js/ac_event.js"></script>

</body>
</html>