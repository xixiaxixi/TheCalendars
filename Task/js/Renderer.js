(function ($)
{
    Renderer = function (canvas_name, screen_padding)
    {
        var canvas = $(canvas_name).get(0);
        var ctx = canvas.getContext("2d");
        var particleSystem = null;
        var nodeBoxes = {};
        var that = {
            init: function (system)
            {
                particleSystem = system;
                particleSystem.screenSize(canvas.width, canvas.height);
                particleSystem.screenPadding(screen_padding);
                that.initMouseHandling();
            },

            redraw: function ()
            {
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                particleSystem.eachNode(function (node, pt)
                                        {
                                            // node: {mass:#, p:{x,y}, name:"", data:{}}
                                            // pt:   {x:#, y:#}  node position in screen coords

                                            // draw a rectangle centered at pt

                                            var name = node.data.title || "未命名任务";
                                            var ddl = node.data.deadline || "结束时间未指定";
                                            var w;
                                            var h;
                                            var re = /[0-9]/;
                                            if (re.test(ddl))//如果有数字，说明是正在进行的任务
                                            {
                                                //设置文字，计算宽，设置高,设置圆角半径
                                                ctx.font = "15px Helvetica";
                                                w = Math.max(ctx.measureText("" + name).width, ctx.measureText("" + ddl).width) + 12;
                                                h = 45;
                                                round_rect(ctx, pt.x - w / 2, pt.y - h / 2, w, h, 5, node.data.color || "rgba(0,0,0,0)");
                                                //画背景框
                                                ctx.textAlign = "center";
                                                ctx.fillStyle = "white";
                                                ctx.fillText(name, pt.x, pt.y - 2);
                                                ctx.fillText(ddl, pt.x, pt.y + 18);
                                                //写文字
                                            }
                                            else //否则是未开始或已完成
                                            {
                                                //设置文字，计算宽，设置高,设置圆角半径
                                                ctx.font = "15px Helvetica";
                                                w = ctx.measureText("" + name).width;
                                                h = 15;
                                                round_rect(ctx, pt.x - w / 2, pt.y - h / 2, w, h, 5, node.data.color || "rgba(0,0,0,0)");
                                                //画背景框
                                                ctx.textAlign = "center";
                                                ctx.fillStyle = "white";
                                                ctx.fillText(name, pt.x, pt.y+5);
                                                ctx.fillStyle = "gray";
                                                ctx.font = "10px Helvetica";
                                                ctx.fillText(ddl, pt.x, pt.y + 20);
                                                //写文字
                                            }
                                            nodeBoxes[node.name] = [pt.x - w / 2, pt.y - h / 2, w, h];
                                            //这个数组里面存了方框的左上点和宽高
                                        });


                particleSystem.eachEdge(function (edge, pt1, pt2)
                                        {
                                            // edge: {source:Node, target:Node, length:#, data:{}}
                                            // pt1:  {x:#, y:#}  source position in screen coords
                                            // pt2:  {x:#, y:#}  target position in screen coords

                                            var tail = intersect_line_box(pt1, pt2, nodeBoxes[edge.source.name]);
                                            var head = intersect_line_box(tail, pt2, nodeBoxes[edge.target.name]);
                                            // draw a line from pt1 to pt2
                                            draw_arrow(ctx, tail, head);
                                            //画箭头
                                        });

            },


            initMouseHandling: function ()
            {

                //鼠标拖动的实现函数

                // no-nonsense drag and drop (thanks springy.js)
                var selected = null;
                var dragged = null;

                // set up a handler object that will initially listen for mousedowns then
                // for moves and mouseups while dragging
                var handler = {
                    clicked: function (e)
                    {

                        //鼠标按下时执行

                        var pos = $(canvas).offset();//获得当前元素的偏移位置坐标
                        var _mouseP = arbor.Point(e.pageX - pos.left, e.pageY - pos.top);//计算点击位置
                        selected = dragged = particleSystem.nearest(_mouseP);//找最近点

                        if (dragged.node !== null) dragged.node.fixed = true;

                        $(canvas).bind('mousemove', handler.dragged);
                        $(window).bind('mouseup', handler.dropped);

                        //鼠标按住移动和放开时分别执行dragged和dropped

                        return false
                    },
                    dragged: function (e)
                    {
                        //拖动时的函数
                        var pos = $(canvas).offset();
                        var s = arbor.Point(e.pageX - pos.left, e.pageY - pos.top);

                        if (!selected) return;
                        if (dragged !== null && dragged.node !== null)
                            dragged.node.p = particleSystem.fromScreen(s);
                        return false
                    },

                    dropped: function (e)
                    {
                        //放开时的函数
                        if (dragged === null || dragged.node === undefined) return;
                        if (dragged.node !== null) dragged.node.fixed = false;
                        dragged.node.tempMass = 50;
                        dragged = null;
                        selected = null;
                        $(canvas).unbind('mousemove', handler.dragged);
                        $(window).unbind('mouseup', handler.dropped);
                        return false
                    }
                };
                $(canvas).mousedown(handler.clicked);

            }
        };
        return that
    };

})(this.jQuery);