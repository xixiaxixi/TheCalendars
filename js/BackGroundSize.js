(function ()
{
    var main_page=$('#MainPage');
    $(window).resize(function ()
                     {
                         resize(canvas, container);
                     });//resize监听
})();