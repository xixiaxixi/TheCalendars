var intersect_line_line = function (p1, p2, p3, p4)
{
    //判断p1p2连线是否与p3p4连线相交
    //如果相交返回交点，否则返回false
    //通过行列式解二元一次方程计算判断
    //https://www.zhihu.com/question/47751368/answer/238729118
    var denom = ((p4.y - p3.y) * (p2.x - p1.x) - (p4.x - p3.x) * (p2.y - p1.y));//向量p1p2和p3p4叉积
    if (denom === 0) return false;// lines are parallel
    var ua = ((p4.x - p3.x) * (p1.y - p3.y) - (p4.y - p3.y) * (p1.x - p3.x)) / denom;
    var ub = ((p2.x - p1.x) * (p1.y - p3.y) - (p2.y - p1.y) * (p1.x - p3.x)) / denom;

    if (ua < 0 || ua > 1 || ub < 0 || ub > 1) return false;
    return arbor.Point(p1.x + ua * (p2.x - p1.x), p1.y + ua * (p2.y - p1.y));
};

//画方框的函数，调用了上面那个intersect_line_line函数判断p1p2连线与boxTuple的边的交点

var intersect_line_box = function (p1, p2, boxTuple)
{
    var p3 = {
            x: boxTuple[0],
            y: boxTuple[1]
        },//方框左上点
        w = boxTuple[2],
        h = boxTuple[3];//宽高

    var tl = {
        x: p3.x,
        y: p3.y
    };//top-left点
    var tr = {
        x: p3.x + w,
        y: p3.y
    };//top-right点
    var bl = {
        x: p3.x,
        y: p3.y + h
    };//bottom-left点
    var br = {
        x: p3.x + w,
        y: p3.y + h
    };//bottom-right点

    return intersect_line_line(p1, p2, tl, tr) ||
        intersect_line_line(p1, p2, tr, br) ||
        intersect_line_line(p1, p2, br, bl) ||
        intersect_line_line(p1, p2, bl, tl) ||
        false
};

var round_rect=function(ctx,x,y,w,h,r,color)
{
    ctx.fillStyle=color;
    ctx.beginPath();
    ctx.moveTo(x + r, y);
    ctx.arcTo(x + w, y, x + w, y + h, r);
    ctx.arcTo(x + w, y + h, x, y + h, r);
    ctx.arcTo(x, y + h, x, y, r);
    ctx.arcTo(x, y, x + w, y, r);
    ctx.closePath();
    ctx.fill();
}

var draw_arrow=function(ctx,tail,head)//从p1到p2画一条箭头
{
    ctx.strokeStyle = "rgba(255,255,255, .4)";
    ctx.lineWidth = 4;
    ctx.beginPath();
    ctx.moveTo(tail.x, tail.y);
    ctx.lineTo(head.x, head.y);
    ctx.stroke();
    //画箭
    ctx.fillStyle = "rgba(255,255,255, .9)";
    ctx.save();
    var arrowLength = 12;
    var arrowWidth = 4;
    ctx.translate(head.x, head.y);
    ctx.rotate(Math.atan2(head.y - tail.y, head.x - tail.x));
    ctx.beginPath();
    ctx.moveTo(-arrowLength, arrowWidth);
    ctx.lineTo(0, 0);
    ctx.lineTo(-arrowLength, -arrowWidth);
    ctx.lineTo(-arrowLength * 0.8, -0);
    ctx.closePath();
    ctx.fill();
    ctx.restore()
    //画头
};