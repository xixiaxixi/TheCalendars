function initGraph(resize)//绘图区域的初始化
{
    var source = "../../ajax/getCanvasData.php?proj_id=" + proj_id;
    $.getJSON(source, function (data)
    {
        sys.graft({
                      nodes: data.nodes,
                      edges: data.edges
                  });

        resize();
    });
}

function deal_changelog(changelog)
{
    switch (changelog.type)
    {
        case 'none':
            break;
        case 'delete':
            delete_task_other(changelog.tid);
            updateGraph();
            break;
        case 'new':
            new_task_other(changelog.tid,changelog.name);
            updateGraph();
            break;
        case 'alter':
            alter_task_other(changelog.tid, changelog.newname);
            updateGraph();
            break;
        case 'finish':
            finish_task_other(changelog.tid, changelog.deadline);
            updateGraph();
            break;
    }
}

function updatePage()//更新页面
{
    var source = "../../ajax/TaskMultiple/changelog.php?proj_id=" + proj_id;
    //var source = "../../ajax/TaskMultiple/get_change_log.php?proj_id=" + proj_id;
    $.getJSON(source, function (changelog)
    {
        console.log(changelog);
        deal_changelog(changelog);
    });
}


function roll_updatePage()
{
    var ajaxTimeoutTest = $.ajax({
                                     url: "../../ajax/TaskMultiple/get_change_log.php",
                                     timeout: 40000, //超时时间设置，单位毫秒
                                     type: 'get',
                                     data: {proj_id: proj_id}, //请求所传参数，json格式
                                     dataType: 'json', //返回的数据格式
                                     success: function (data)
                                     {
                                         console.log(data);
                                         deal_changelog(data);
                                         roll_updatePage();
                                     },
                                     complete: function (XMLHttpRequest, status)
                                     { //求完成后最终执行参数
                                         // 设置timeout的时间，通过检测complete时status的值判断请求是否超时，如果超时执行响应的操作
                                         console.log(status);
                                         if (status === 'timeout')
                                         { //超时,status还有success,error等值的情况
                                             ajaxTimeoutTest.abort();
                                             roll_updatePage();
                                         }
                                     }
                                 });
}
    /*
        var source = "../../ajax/TaskMultiple/get_change_log.php?proj_id=" + proj_id;
        $.getJSON(source, function (changelog)
        {
            console.log(changelog);
            switch (changelog.type)
            {
                case 'delete':
                    delete_task_other(changelog.tid);
                    break;
                case 'new':
                    new_task_other(changelog.tid);
                    break;
                case 'alter':
                    alter_task_other(changelog.tid);
                    break;
                case 'finish':
                    finish_task_other(changelog.tid);
                    break;
            }
            updateGraph();
            setTimeout(roll_updatePage, 500);
        });//取消请求未做
}*/

function delete_task_other(task_id)//接到删除消息
{
    $('.task-div[id="' + proj_id + ' ' + task_id + '"]').remove();
    remove_all_check_box(task_id);//删掉前继复选框
    var index = tasks_id.indexOf(parseInt(task_id));
    if (index > -1) tasks_id.splice(index, 1);//数组中减去这一项
    swiper_adj();
}

function new_task_other(task_id,task_name)//接到新建消息
{
    add_new_checkbox(task_id, task_name);
    var newdiv = $('#new-task');
    if (newdiv.parent().children('.task-div[id="' + proj_id + ' ' + task_id + '"]').length <= 0)
        newdiv.after(getTaskDiv(task_id));
    hide_alter();
    tasks_id.push(parseInt(task_id));//数组中加一项
}

function alter_task_other(task_id, newname)//接到修改消息
{
    update_all_check_box(task_id, newname);//先更新所有label
    var newdiv = getTaskDiv(task_id);
    $('.task-div[id="' + proj_id + ' ' + task_id + '"]').replaceWith(newdiv);//再替换这个div
}

function finish_task_other(task_id, deadline)//接到完成消息
{
    deadline = deadline.split(' ');
    var form = $('.task-div[id="' + proj_id + ' ' + task_id + '"]').find('form');
    form.find('.ddl[type=date]').val(deadline[0].replace(/[\r\n]/g, ""));
    form.find('.ddl[type=time]').val(deadline[1].replace(/[\r\n]/g, ""));
}

function updateGraph()//绘图区域的更新
{
    var source = "../../ajax/getCanvasData.php?proj_id=" + proj_id;
    $.getJSON(source, function (data)
    {
        var count_exists = 0;
        sys.eachNode(function ()
                     {
                         count_exists++;
                     });

        console.log(count_exists);
        var count_new = 0;
        for (a in data.nodes)
        {
            count_new++;
            if (count_new >= 2) break;
        }
        if (count_exists === 1 && count_new > 1)//先计算点数，如果是从一到二会卡
        {
            sys.prune(function ()
                      {
                          return true;
                      });
            updateGraph();
        }
        else sys.merge(data);
    });
}


function getTaskDiv(task_id)//获取taskdiv
{
    var task_div = '';

    $.ajax({
               type: "post",
               url: "../ajax/TaskMultiple/get_task_div.php",
               data: "task_id=" + task_id + "&proj_id=" + proj_id,
               async: false,
               success: function (data)
               {
                   if (data.includes("error"))
                   {
                       alert(data);
                       location.reload();
                   }
                   else
                   {
                       task_div += data;
                   }
               }
           });
    return task_div;
}


/*
function updatePage()
{
    updateList();
    updateGraph();
}


function updateList()//请求数据并比对
{
    var source = "../../ajax/TasksList.php?proj_id=" + proj_id;
    $.getJSON(source, function (data)
    {
        console.log(data);
        var task_todel = tasks_id.slice(0);
        var task_toadd = data.slice(0);
        if (JSON.stringify(data) !== JSON.stringify(tasks_id))
        {
            diff(task_todel, task_toadd);//task_toadd中的是要增加的div，task_todel中是要删除的div

            var i;
            for (i in task_todel)
                delete_task_other(task_todel[i]);//删掉多的

            for (i in task_toadd)
                new_task_other(task_toadd[i]);//增加少的
        }
        tasks_id = data.slice(0);//重新赋值
    });
}

function diff(array1, array2)
{
    var i = 0;
    var index;
    while (i < array1.length)
    {
        index = array2.indexOf(array1[i]);
        if (index > -1)//若找到相同元素
        {
            array1.splice(i, 1);
            array2.splice(index, 1);//则删除
            continue;//且索引位置不变
        }
        i++;//不是相同元素则找下一个
    }
}*/