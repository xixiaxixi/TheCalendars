
var username = "";
var excep_msg = "Exception!!!\n" +
    "Cookie Failed to Get Username\n" +
    "Re Log in or Contact Admin";

var last_result;

var disp_state = {
    "t" : 1,
    "cr" : 1,
    "day" : 1,
    "pr": 1,
    "wk" : 1,
    "isc" :1
};

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

function add_elec(no) {
    console.log(last_result[no]);
    if (username == "") {
        alert(excep_msg);
        return;
    }

    console.log('add');
    console.log(last_result[no]);
    $.post("phps/add_elec.php", {
        "username": username,
        'coursecode': last_result[no]['课程代码'],
        'courseno': last_result[no]['课程编号']
    }, function (json) {
        console.log(json);
        // notes = JSON.parse(json);
        var res = json;
        if (res['state'] == 'sc') {
            btn_search_click();
        } else if (res['state'] == 'error') {
            alert("oh, error.")
        }
    }, "json");

}

function del_elec(no) {
    if (username == "") {
        alert(excep_msg);
        return;
    }

    console.log('del');
    console.log(last_result[no]);
    $.post("phps/del_elec.php", {
        "username": username,
        'coursecode': last_result[no]['课程代码'],
        'courseno': last_result[no]['课程编号']
    }, function (json) {
        console.log(json);
        // notes = JSON.parse(json);
        var res = json;
        if (res['state'] == 'sc') {
            btn_search_click();
        } else if (res['state'] == 'error') {
            alert("oh, error.")
        }
    }, "json");
}

function rev_display(sender) {
    if (disp_state[sender] == 1) {
        disp_state[sender] = 0;
        $("." + sender).css("display", "none");
        $("#" + sender).attr("style", "background:rgba(0, 0, 0, 0.3)")
    } else {
        disp_state[sender] = 1;
        $("." + sender).css("display", "");
        $("#" + sender).attr("style", "background:rgba(0, 255, 10, 0.3)")
    }
}

function _num_convert_capital(d) {
    switch (d) {
        case 1:
            return '一';
        case 2:
            return "二";
        case 3:
            return "三";
        case 4:
            return "四";
        case 5:
            return "五";
    }
}

function _create_td_elec(x) {
    return $("<td></td>").append(
        $('<span class="cn">' + x['课程名称'] + '<br/></span>'),
        $('<span class="t">' + x['教师'] + '&nbsp;&nbsp;</span>'),
        $('<span class="cr">' + x['教室'] + '<br/></span>'),
        $('<span class="day">周' + _num_convert_capital(x['周上课日']) + '&nbsp;&nbsp;</span>'),
        $('<span class="pr">第' + x['起始节数'] + '-' + (parseInt(x['起始节数']) + parseInt(x['持续节数']) - 1) + '节&nbsp;&nbsp;</span>'),
        $('<span class="wk">第' + x['开始周'] + '-' + x['结束周'] + '周' + (x['单双周'] == "/" ? "" : "(" + x['单双周'] + "周)") + '</span>'),
        $('<span class="isc"><br/>是否自选: ' + x['是否蹭课'] + '</span>')
    );
}

function _create_td_search(x) {
    return $("<td></td>").append(
        $('<span class="cn">' + x['课程名称'] + '<br/></span>'),
        $('<span class="t">' + x['教师'] + '&nbsp;&nbsp;</span>'),
        $('<span class="cr">' + x['教室'] + '<br/></span>'),
        $('<span class="day">周' + _num_convert_capital(x['周上课日']) + '&nbsp;&nbsp;</span>'),
        $('<span class="pr">第' + x['起始节数'] + '-' + (parseInt(x['起始节数']) + parseInt(x['持续节数']) - 1) + '节&nbsp;&nbsp;</span>'),
        $('<span class="wk">第' + x['开始周'] + '-' + x['结束周'] + '周' + (x['单双周'] == "/" ? "" : "(" + x['单双周'] + "周)") + '</span>')
    );
}

function create_tr(content, type, btn_event) {
    var tr = $("<tr></tr>");
    var td0 = $("<td></td>");
    td0.append(content);
    tr.append(td0);
    var td1 = $("<td></td>");
    var btn = $("<button></button>");
    btn.attr("onclick", btn_event);
    switch (type) {
        case 'add':
            btn.append('添 加');
            break;
        case 'del':
            btn.append('删 除');
            break;
        case 'ad':
            btn.append('去看看');
            break;
        case'back':
            btn.append('返 回');
            break;
        case 'much':
            btn.append('　...　');
            break;
    }
    td1.append(btn);
    tr.append(td1);
    return tr;
}

function draw_table_elec(table) {
    var tb = $(".result>tbody");
    $(".result>tbody>tr").remove();
    for (i = 0; i < table.length; i++) {
        var x = table[i];
        var td1content = _create_td_elec(x);
        tb.append(create_tr(td1content, 'del', 'del_elec('+i+')'));

        if (i % 20 == 19) {
            //TODO: ADD ADVERTISEMENT HERE
        }
    }
}

function draw_table_search(res) {
    var tb = $(".result>tbody");
    $(".result>tbody>tr").remove();

    for (i = 0; i < res.length; i++) {
        var x = res[i];
        var td1content = _create_td_search(x);
        if (x['是否蹭课'] == null) {
            tb.append(create_tr(td1content, 'add', 'add_elec(' + i + ')'));
        } else {
            tb.append(create_tr(td1content, 'del', 'del_elec(' + i + ')'));
        }

        if (i % 20 == 19) {
            //TODO: ADD ADVERTISEMENT HERE
        }
    }
    if (res.length == 100) {
        tb.append(create_tr('结果太多了<br/>加点关键词吧', '', null));
    }
}

function load_has_elec(us) {
    console.log("loading...");
    $.post("phps/get_has_elec.php", {"username": us}, function (json) {
        console.log(json);
// notes = JSON.parse(json);
        var res = json;
        if (res['state'] == 'sc') {
            res = json['table'];
            last_result = res;
            draw_table_elec(res);
        } else if (res['state'] == 'noRecord') {
            console.log("noRecord");
        }

        swiper.update();
    }, "json");
}

function load_search(us, txt) {
    console.log("searching...");
    $.post("phps/search_course.php", {"word": txt, "username": us}, function (json) {
        console.log(json);
        // notes = JSON.parse(json);
        var res = json;
        if (res['state'] == 'sc') {
            res = json['result'];
            last_result = res;
            draw_table_search(res);
        } else if (res['state'] == 'noResult') {
            console.log("noRecord");
            $(".result>tbody>tr").remove();
            $(".result>tbody").append(create_tr('这里，空的', 'back', null));
        }

        swiper.update();
    }, "json");
}

function btn_search_click() {
    if (username == "") {
        alert(excep_msg);
        return;
    }

    var txt = $(".text_area")[0].value;
    txt = txt.trim();
    if (txt == "") {
        load_has_elec(username);
    } else {
        load_search(username, txt);
    }
    new Swiper('.swiper-container', {
        slidesPerView: 'auto',
        centeredSlides: true,
        spaceBetween: 30,
        initialSlide: 1
    });
    // new Swiper('.sub-swiper-container', {
    //     direction: 'vertical',
    //     slidesPerView: 'auto',
    //     freeMode: true,
    //     scrollbar: {
    //         el: '.swiper-scrollbar'
    //     },
    //     mousewheel: true,
    //     observer: true,
    //     observeParents: true
    // });
}

$(document).ready(function () {
    username = getCookie("logname");

    if (username == "") {
        alert(excep_msg);
        return;
    }

    load_has_elec(username);
});
