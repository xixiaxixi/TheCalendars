var sys;
var proj_id;
var tasks_id;
var screen_padding;//这是个全局变量
var canvas;//绘图的东西
var container;//绘图外面包着的东西


//动画相关函数
function swiper_adj()//更新swiper
{
    swiper.update();
}

function canvas_width()//计算绘图区域宽度
{
    var width = $(window).width() - 20;
    if (width >= 1000) return width - 500;
    else if (width >= 500) return 500;
    else if (width >= 300) return width;
    else return 300;
}

function resize(canvas, container)//重设窗口大小
{
    canvas.width = canvas_width();
    canvas.height = $(window).height() - 20;
    sys.screenSize(canvas.width, canvas.height);
    sys.renderer.redraw();
    if ($(container).width() >= 100)
    {
        $(container).width(canvas.width);
        $(container).height(canvas.height);
    }
}

function hide_alter()//隐藏所有表单
{
    $('.task-form').each(function ()
                         {
                             $(this).slideUp('fast', function ()
                             {
                                 swiper_adj();
                             });
                         });
    $("#new-task").slideUp("fast", function ()
    {
        swiper_adj();
    });
}

//checkbox的新建/更新/删除
function add_new_checkbox(task_id, task_name)//在每个task-div里面添加新建项的checkbox
{
    var add_checkbox = "<input type='checkbox' name='task[]' value='" + task_id + "'/>";
    add_checkbox += "<span class='checkbox-label for" + task_id + "'>" + task_name + "</span><br>";
    $('.task-div').each(function ()
                        {
                            if ($(this).find('.alter-button:contains("View")').length <= 0)//不是只读才改
                                $(this).find('legend:contains("Formers")').first().after(add_checkbox);
                        });
}

function remove_all_check_box(task_id)//从每个task-div里面删除checkbox
{
    $('input[type="checkbox"][name^="task"][value="' + task_id + '"]').each(function ()
                                                                            {
                                                                                $(this).remove()
                                                                            });
    $('span.for' + task_id).each(function ()
                                 {
                                     $(this).remove()
                                 });
}

function update_all_check_box(task_id, task_name)//在每个task-div里面更新checkbox的名字
{
    $('span.for' + task_id).each(function ()
                                 {
                                     $(this).text(task_name)
                                 });
}


//新建时的必要函数
function change_new_task_div(old_add_div, task_id)//修改新建表单
{
    var add_btn = "<div class='button-set'> <button class='opera-button alter-button'>Alter</button> <button class='opera-button finish-button'>Finish</button> <button class='opera-button delete-button'>Delete</button> </div>";
    old_add_div.removeAttr("style");
    old_add_div.attr("id", parseInt(proj_id).toString() + ' ' + parseInt(task_id).toString());// proj_id + ' ' + task_id);
    old_add_div.find(".task-title").html(old_add_div.find("form").find(".task-name").val()).after(add_btn);
    old_add_div.find("form").find(".add-button").attr("class", 'opera-button update-button');
}

//task-div的新建/更新/删除
function add_task_div_self(task_id, form)//自己改的taskdiv的新建
{
    //if($('.task-div').size()===2)
    //sys.prune(function () {return true;});//加点从1到2时会卡死，arbor.js的bug
    task_id = parseInt(task_id).toString();
    //拷贝->其他的部分添加复选框->修改拷贝->写回
    var old_add_div = form.parent();//选取原有新建表单
    var new_div = old_add_div.clone();//复制保存

    var task_name = old_add_div.find('.task-name').val();
    add_new_checkbox(task_id, task_name);//在所有的taskdiv下面加上新建的任务复选框

    change_new_task_div(new_div, task_id);//改掉复制来的表单

    old_add_div.slideUp('fast', function ()
    {
        swiper_adj();
    });
    old_add_div.after(new_div);//在原有add-div后面加上方才复制保存的表单
    hide_alter();
    tasks_id.push(parseInt(task_id));//数组中加一项
}

function del_task_div_self(task_id, task_div)//自己改的taskdiv的删除
{
    task_div.remove();
    remove_all_check_box(task_id);//删掉前继复选框
    var index = tasks_id.indexOf(parseInt(task_id));
    if (index > -1) tasks_id.splice(index, 1);
    //数组中减去这一项
    swiper_adj()
}

function update_task_div_self(task_id, form)//自己改的taskdiv的更新
{
    var task_name = form.find('.task-name').val();
    form.prev().find('.task-title').text(task_name);
    update_all_check_box(task_id, task_name);
    hide_alter();
    //更新所有前继复选框
}