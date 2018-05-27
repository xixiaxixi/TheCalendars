$(document).ready(function ()
                  {
                      //以下是页面初始化代码
                      screen_padding=80;
                      canvas = $('.main-pic').get(0);//获取绘图部分
                      container = $('.main-pic-container').get(0);//获取绘图外面的框
                      proj_id = $(".add-button").attr('id');//获取工程id
                      tasks_id = new Array(0);//初始化维护一个数组，存储左边有的div任务代码
                      $(document).find(".task-div[id!='new-task']").each(function ()
                                                                         {
                                                                             tasks_id.push(parseInt($(this).attr('id').split(' ')[1]));
                                                                         });
                      tasks_id.sort();//获读取页面上的所有任务代码

                      sys = arbor.ParticleSystem(canvas_width(), $(window).height(), 0.5);
                      sys.renderer = Renderer('.main-pic', screen_padding);
                      sys.renderer.init(sys);//绘图系统初始化
                      initGraph(function ()
                      {
                          resize(canvas, container);
                      });//绘图数据初始化

                      $(window).resize(function ()
                                       {
                                           resize(canvas, container);
                                       });//resize监听

                      if ($(window).width() - 20 <= 1000)
                      {
                          $(container).width(0);
                          $(container).height(0);
                      }
                      else
                      {
                          $(container).width(canvas.width);
                          $(container).height(canvas.height);
                      }//绘图窗口初始化，浏览器窗口宽度不足1800时不显示绘图部分


                      //所有的动画事件
                      $(document).on("click", ".show-add-div", function ()//动画事件：显示新建任务表单
                      {
                          var new_task_div = $("#new-task");
                          if (new_task_div.css('display') === 'none')//如果没显示
                          {
                              hide_alter();
                              new_task_div.slideDown("fast");//就显示
                              new_task_div.find(".task-form").slideDown("fast", function ()
                              {
                                  swiper_adj();
                              });
                          }
                          else new_task_div.slideUp("fast", function ()
                          {
                              swiper_adj();
                          });//否则就隐藏
                      });
                      $(document).on("click", ".alter-button", function ()//动画事件：显示修改任务表单
                      {
                          var form = $(this).closest(".task-head").next();
                          if (form.css('display') === 'none')//如果没有显示
                          {
                              hide_alter();
                              form.slideDown("fast", function ()
                              {
                                  swiper_adj();
                              });//就显示
                          }
                          else//如果有显示
                          {
                              hide_alter();//就收回
                          }
                      });
                      $(document).on("click", ".cancel-button", function ()//动画事件：隐藏表单
                      {
                          hide_alter();
                      });
                      $(document).on("click", ".checkbox-label", function ()//动画事件：点击label
                      {
                          var checkbox = $(this).prev();
                          if (checkbox.attr('checked') === 'checked')
                              checkbox.removeAttr('checked');
                          else checkbox.attr('checked', 'checked');
                      });
                      $(document).on("click", ".toggle-flow-pic", function ()//动画事件：显示/隐藏流程图
                      {
                          $(this).animate({rotate: '360'}, 500);
                          var container = $('.main-pic-container');
                          var canvas = $('.main-pic');
                          if (container.width() < 100)
                              container.animate({
                                                    width: canvas.width() + 'px',
                                                    height: canvas.height() + 'px'
                                                }, 500);
                          else
                              container.animate({
                                                    width: '0px',
                                                    height: '0px'
                                                }, 500);
                      });


                      //所有的数据事件
                      $(document).on("click", ".add-button", function ()//数据事件：新建任务
                      {
                          var form = $(this).closest(".task-form");
                          var options = {
                              url: '../ajax/TaskMultiple/new_task.php?gid=' + proj_id, //上传文件的路径
                              type: 'post',
                              success: function (task_id)//返回任务代码
                              {
                                  if (task_id.includes("error"))
                                  {
                                      alert(task_id);
                                      location.reload();
                                  }
                                  else
                                  {
                                      add_task_div_self(task_id, form);
                                      updateGraph();
                                  }//在表格里新加一项
                              },
                              error: function ()
                              {
                                  alert("操作失败，请联系管理员");
                                  location.reload();
                              }
                          };
                          form.ajaxSubmit(options);
                      });


                      $(document).on("click", ".delete-button", function ()//数据事件：删除任务
                      {
                          var task_div = $(this).closest(".task-div");
                          var task_title = task_div.find('.task-title').text();
                          if (!confirm('Delete the Task ' + task_title + '?')) return;
                          var task_id = task_div.attr('id').split(' ')[1];
                          $.post('../ajax/TaskMultiple/de_task.php', {
                              tid: task_id,
                              pid: proj_id
                          }, function (data)
                                 {
                                     if (data.includes("error"))
                                     {
                                         alert(data);
                                         location.reload();
                                     }
                                     else
                                     {
                                         del_task_div_self(task_id, task_div);
                                         updateGraph();
                                     }
                                 });
                      });


                      $(document).on("click", ".finish-button", function ()//数据事件：一键完成
                      {
                          var task_div = $(this).closest(".task-div");
                          var task_id = task_div.attr('id').split(' ')[1];
                          $.post('../ajax/TaskMultiple/finish_task.php', {
                              tid: task_id,
                              pid: proj_id
                          }, function (data)
                                 {
                                     if (data.includes("error"))
                                     {
                                         alert(data);
                                         location.reload();
                                     }
                                     else
                                     {
                                         var form = task_div.find('form');
                                         var deadline = data.split(' ');
                                         form.find('.ddl[type=date]').val(deadline[0].replace(/[\r\n]/g, ""));
                                         form.find('.ddl[type=time]').val(deadline[1]);
                                         updateGraph();
                                     }
                                 });
                      });


                      $(document).on("click", ".update-button", function ()//数据事件：更新任务
                      {
                          var task_div = $(this).closest(".task-div");
                          var task_id = task_div.attr('id').split(' ')[1];
                          var form = $(this).closest(".task-form");
                          var options = {
                              url: '../ajax/TaskMultiple/alter_task.php?tid=' + task_id + '&gid=' + proj_id, //上传文件的路径
                              type: 'post',
                              success: function (data)
                              {
                                  if (data.includes("error"))
                                  {
                                      alert(data);
                                      location.reload();
                                  }
                                  else
                                  {
                                      alert('修改成功');
                                      update_task_div_self(task_id, form);
                                      updateGraph();
                                  }
                              },
                              error: function ()
                              {
                                  alert("更新失败");
                                  location.reload();
                              }
                          };
                          form.ajaxSubmit(options);
                      });



                      roll_updatePage();
                      //setInterval("updatePage()", "1000");//设置心跳数据包
                  });
