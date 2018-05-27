$(document).ready(function(){
    $(document).on("click",".a",function(){
        $(this).slideToggle();
    });
    $("button").click(function(){
        $("<p><span  class='a'>This is</span> a new paragraph.</p>").insertAfter("button");
    });
});