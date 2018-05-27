var i_richen_is_hidden = 1;
var a_course_is_hidden = 1;

var username = "";
var excep_msg = "Exception!!!\n" +
    "Cookie Failed to Get Username\n" +
    "Re Log in or Contact Admin";

var div_loading = $("<div class=\"spinner\">\n" +
    "  <div class=\"rect1\"></div>\n" +
    "  <div class=\"rect2\"></div>\n" +
    "  <div class=\"rect3\"></div>\n" +
    "  <div class=\"rect4\"></div>\n" +
    "  <div class=\"rect5\"></div>\n" +
    "</div>");

function getCookie(c_name) {
    if (document.cookie.length>0)
    {
        c_start=document.cookie.indexOf(c_name + "=");
        if (c_start!=-1)
        {
            c_start=c_start + c_name.length+1;
            c_end=document.cookie.indexOf(";",c_start);
            if (c_end==-1) c_end=document.cookie.length;
            return unescape(document.cookie.substring(c_start,c_end))
        }
    }
    return ""
}

var timeOutEvent=0;
function setTouchEvent(target, shortPress, longPress) {
    $(function(){
        target.on({
            touchstart: function (e) {
                console.log("ontouch");
                timeOutEvent = setTimeout(longPress, 500);
                // e.preventDefault();
                return true;
            },
            touchmove: function () {
                console.log("touchmove");
                clearTimeout(timeOutEvent);
                timeOutEvent = 0;
                return true;
            },
            touchend: function () {
                console.log("touchend");
                clearTimeout(timeOutEvent);
                if (timeOutEvent != 0) {
                    eval(shortPress);
                }
                return true;
            }
        });
        target.mousedown(function () {
            console.log('mouse down');
            timeOutEvent = setTimeout(longPress, 500);
            return true;
        });
        target.mouseleave(function () {
            console.log('mouse leave');
            clearTimeout(timeOutEvent);
            timeOutEvent = 0;
            return true;
        });
        target.mouseup(function () {
            console.log('mouse up');
            clearTimeout(timeOutEvent);
            if (timeOutEvent != 0) {
                eval(shortPress);
            }
            return true;
        });
    });
}

function rev_i_hidden() {
    if (i_richen_is_hidden) {
        $("#input_add_richen").slideDown();
        i_richen_is_hidden = 0;
    } else {
        $("#input_add_richen").slideUp();
        i_richen_is_hidden = 1;
    }
}

function rev_a_hidden() {
    if (a_course_is_hidden) {
        $("#input_load_course").slideDown();
        a_course_is_hidden = 0;
    } else {
        $("#input_load_course").slideUp();
        a_course_is_hidden = 1;
    }
}

function insert_richen(){
    if (username == "") {
        alert(excep_msg);
        return;
    }

    console.log("add richen");
    var i_dt = $("#input_add_richen input")[0];
    var i_tn = $("#input_add_richen textarea")[0];
    var i_td = $("#input_add_richen textarea")[1];
    console.log(i_tn.value);
    console.log(i_td.value);
    console.log(i_dt.value);
    if (i_dt.value == "" || i_tn.value.trim() == "") {
        alert("输入日程名和时间哦");
    } else {
        $.post("phps/add_richen.php", {
            "username": username,
            "taskname": i_tn.value,
            "taskdetail": i_td.value,
            "deadline": i_dt.value
        }, function (json) {
            if (json['state'] == 'sc') {
                rev_i_hidden();
                load_richen_project(username);
            } else {
                console.log(json);
                rev_i_hidden();
                alert('添加失败');
            }
        }, "json");
    }
}

function delete_richen(tn, td, dt) {
    if (username == "") {
        alert(excep_msg);
        return;
    }
    if (!confirm("确认删除这个日程？")) {
        return;
    }
    $.post("phps/del_richen.php", {
        "username": username,
        "taskname": tn,
        "taskdetail": td,
        "deadline": dt
    }, function (json) {
        if (json['state'] == 'sc') {
            load_richen_project(username);
        } else {
            console.log(json);
            alert('删除失败');
        }
    }, "json");
}

function jump_to_pro(pro_id) {
    window.open("Task/Project.php?proj_id="+pro_id, "_self");
}

//---- load courses from jwc ----
//--lun xun--
function get_zf_state() {
    console.log("checking...");
    $.post("phps/check_state.php", {
        "username": username
    }, function (json) {
        console.log(json);
        if (json['state'] == 'sc') {
            load_ct(username);
            rev_a_hidden();
            $("#btn_load_course").html('获 取');
        } else if (json['state'] == 'ld') {
            setTimeout(get_zf_state, 666);
        } else {
            switch (json['state']) {
                case 'ue':
                    alert('正方用户名错误');
                    break;
                case 'ke':
                    alert('正方密码错误');
                    break;
                case 'ye':
                case 'te':
                default:
                    alert('卧槽，抽风了');
            }
            $("#btn_load_course").html('获 取');
        }
        $("#btn_load_course").attr('onclick', 'add_course()');
    }, "json");
}
function add_course() {
    if (username == "") {
        alert(excep_msg);
        return;
    }

    var acc = $("#ipt_zf_acc")[0].value.trim();
    var pswd = $("#ipt_zf_pswd")[0].value;
    if (acc == "") {
        alert("不输入账号怎么行呢");
        return;
    }
    if (acc.length < 10) {
        alert("账号只能是10位以上哦");
        return;
    }
    if (pswd == "") {
        alert("不打密码怎么行嘛");
        return;
    }

    console.log('zf:' + acc);
    console.log('zf:' + pswd);
    $.post("phps/load_courses.php", {
        'username': username,
        'zfname': acc,
        'zfpswd': pswd
    }, function (json) {
        console.log(json);
        if (json['state'] == 'ok') {
            $("#btn_load_course").html(div_loading);
            $("#btn_load_course").attr('onclick', '');
            if (json['queue'] > 5) {
                alert('访问人数多,waiting please');
            }
            get_zf_state();
        }
    });
}

$(document).ready(function () {
    $("#input_add_richen").hide();
    $("#input_load_course").hide();
    username = getCookie("logname");
});
