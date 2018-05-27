var iii = 0;

function create_one_note(time, title, detail, fr, pro_id) {
    console.log('adding');
    var ev_block =  $("<div class='event_block'></div>");
    ev_block.append($("<time></time>").text(time),
        $("<h2></h2>").text(title),
        $("<p></p>").text(detail));
    switch (fr) {
        case 'ric':
            setTouchEvent(ev_block, "", "delete_richen('" +
                title +
                "','" +
                detail +
                "','" +
                time +
                "')");
            break;
        case 'pro':
            setTouchEvent(ev_block, "", "jump_to_pro(" +
                pro_id +
                ")")
            break;
        case  'ad':
            break;
    }
    if (time != "广告") {
        console.log("adding listener");
        // setTouchEvent(ev_block, "", `delete_richen("${title}","${detail}","${time}")`);


    }
    return ev_block;
}

function _draw_tr(ct_json, tb, tr, st, l) {
    var empt_td = "<td style='background-color: rgba(0,0,0,0)'></td>";
    var leftday = 5;
    var leftday2 = 5;
    for (var x = 1; x < 6; x++){
        if (iii == ct_json.length) {
            // var td = $("<td></td>");
            // tr.append(td);
            tr.append(empt_td);
            continue;
        }
        if (ct_json[iii]['周上课日'] == x && ct_json[iii]['起始节数'] == st) {
            var td = $("<td></td>");
            var course_name = $("<span></span>");
            course_name.text(ct_json[iii]['课程名称']);
            course_name.css("font-size", "16px");
            var course_teacher = $("<span></span>");
            var classroom = ct_json[iii]['教室'];
            if (classroom.length > 0) {
                if (classroom.charAt(classroom.length - 1) == "多") {
                    classroom = classroom.slice(0, -1);
                }
            }
            course_teacher.text(classroom);
            course_teacher.css("font-size", "14px");

            td.append(course_name, "<br/>", course_teacher);
            var length = ct_json[iii]['持续节数'];

            do {
                if (iii + 1 == ct_json.length) {
                    break;
                } else {
                    if (ct_json[iii + 1]['周上课日'] == x && ct_json[iii + 1]['起始节数'] == st) {
                        iii++;
                        var course_name = $("<span></span>");
                        course_name.text(ct_json[iii]['课程名称']);
                        course_name.css("font-size", "16px");
                        var course_teacher = $("<span></span>");
                        var classroom = ct_json[iii]['教室'];
                        if (classroom.length > 0) {
                            if (classroom.charAt(classroom.length - 1) == "多") {
                                classroom = classroom.slice(0, -1);
                            }
                        }
                        course_teacher.text(classroom);
                        course_teacher.css("font-size", "14px");
                        td.append("<br/><br/>", course_name, "<br/>", course_teacher);
                        length = length > ct_json[iii]['持续节数'] ? length : ct_json[iii]['持续节数'];
                    } else {
                        break;
                    }
                }
            } while (1);

            td.attr("rowspan", length);
            tr.append(td);
            if (length >= 2) {
                leftday--;
            }
            if (length >= 3) {
                leftday2--;
            }
            iii++;
        } else {
            // var td = $("<td></td>");
            // tr.append(td);
            tr.append(empt_td);
        }
    }
    tb.append(tr);
    if (l >= 2) {
        var tr = $("<tr></tr>");
        for (var j = 0; j < leftday; j++) {
            // tr.append("<td></td>");
            tr.append(empt_td);
        }
        tb.append(tr);
    }
    if (l >= 3) {
        var tr = $("<tr></tr>");
        for (var j = 0; j < leftday2; j++) {
            // tr.append("<td></td>");
            tr.append(empt_td);
        }
        tb.append(tr);
    }
}

function draw_course_table(ct_json) {
    $(".coursetable tr").remove();
    var tb = $(".coursetable>tbody");
    tb.append("<tr><th></th><th>周一</th><th>周二</th><th>周三</th><th>周四</th><th>周五</th></tr>");
    iii = 0;
    for (var y = 1; y < 13; y++){
        var tr = $("<tr></tr>");
        switch (y){
            case 1:
                tr.append("<th rowspan=\"5\">上午</th>");
                _draw_tr(ct_json, tb,tr, 1,2);
                break;
            case 3:
                _draw_tr(ct_json, tb,tr, 3,3);
                break;
            case 6:
                tr.append("<th rowspan=\"4\">下午</th>");
                _draw_tr(ct_json, tb,tr, 6,2);
                break;
            case 8:
                _draw_tr(ct_json, tb,tr, 8,2);
                break;
            case 10:
                tr.append("<th rowspan=\"3\">晚上</th>");
                _draw_tr(ct_json, tb,tr, 10, 3);
                break;
        }
    }
}

function load_richen(us) {
    $.post("phps/get_richen.php", {"username": us}, function (json) {
        console.log(json);
        // notes = JSON.parse(json);
        notes = json;
        if (notes['state'] == 'sc') {
            notes = notes['richen'];
            console.log(notes);
            for (i = 0; i < notes.length; i++) {
                // console.log(x);
                x = notes[i];
                $("#event_axis").append(create_one_note(x[2], x[0], x[1]));
            }
        }
    }, "json");
}

function load_project(us) {
    $.post("phps/get_project.php", {"username": us}, function (json) {
        console.log(json);
        // notes = JSON.parse(json);
        notes = json;
        if (notes['state'] == 'sc') {
            notes = notes['project'];
            console.log(notes);
            for (i = 0; i < notes.length; i++) {
                // console.log(x);
                x = notes[i];
                $("#event_axis").append(create_one_note(x[2], x[0], x[1]));
            }
        }
    }, "json");
}

function load_richen_project(us) {
    $(".event_block").remove();
    $.post("phps/get_time_axis.php", {"username": us}, function (json) {
        console.log('richen json:');
        console.log(json);
        // notes = JSON.parse(json);
        var notes = json;
        if (notes['state'] == 'sc') {
            notes = notes['event'];
            console.log(notes);
            var ea = $("#event_axis");
            for (i = 0; i < notes.length; i++) {
                // console.log(x);
                x = notes[i];
                ea.append(create_one_note(x[2], x[0], x[1], x[3], x[4]));
                if (i % 7 == 6) {
                    ea.append(create_one_note('', '广告位招租', '', 'fr', ''));
                }
            }
        }
        swiper.update();
    }, "json");
}

function load_ct(us) {
    $.post("phps/get_has_elec.php", {"username": us}, function (json) {
        console.log('table get');
        console.log(json);
        // notes = JSON.parse(json);
        var res = json;
        if (res['state'] == 'sc') {
            draw_course_table(res['table']);
        } else if (res['state'] == 'noRecord') {

        }
    }, "json");
}

function load_ct_new() {
    $("#btn_load_course").html(div_loading);
    $("#btn_load_course").attr('onclick', '');
    $.post("phps/check_state.php", {
        "username": username
    }, function (json) {
        console.log(json);
        if (json['state'] == 'sc') {
            load_ct(username);
            a_course_is_hidden = 1;
            $("#input_load_course").slideUp();
            $("#btn_load_course").html('获 取');
        } else if (json['state'] == 'ld') {
            setTimeout(load_ct_new, 666);
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
                case 'em':
                    break;
                default:
                    alert('卧槽，抽风了');
            }
            $("#btn_load_course").html('获 取');
        }
        $("#btn_load_course").attr('onclick', 'add_course()');

        swiper.update();
    }, "json");
}

$(document).ready(function () {
    if (username == "") {
        alert(excep_msg);
        return;
    }
    // $(".swiper-container").setAttribute("observer", "true");
    console.log(username);
    console.log('auto load');
    load_ct_new();
    load_richen_project(username);
});
