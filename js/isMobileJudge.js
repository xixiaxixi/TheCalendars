(function isMobile_setCookie()
{
    var xmlhttp;
    if (window.XMLHttpRequest)
    {
        xmlhttp = new XMLHttpRequest();
    }
    else
    {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function ()
    {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
        {
            document.cookie += ';isMobile=' + xmlhttp.responseText;
        }
    };
    xmlhttp.open("GET", "ajax/isMobile.php", true);
    xmlhttp.send();
})();
