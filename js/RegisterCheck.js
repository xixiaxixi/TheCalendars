function check_logname()
{
    confirmicon = document.getElementById("logname_confirm_icon");
    submit_btn = document.getElementById("submit_button");
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
            if (xmlhttp.responseText)
            {
                confirmicon.style.visibility = "visible";
                submit_btn.disabled = true;
            }
            else
            {
                confirmicon.style.visibility = "hidden";
                submit_btn.disabled = false;
            }
        }
    };
    logname = document.getElementById("logname").value;
    if(logname.length<=6 ||!(/^[0-9a-zA-Z]+$/).test(logname))
    {
        confirmicon.style.visibility = "visible";
        submit_btn.disabled = true;
    }
    else
    {
        xmlhttp.open("POST", "ajax/register_name_check.php", true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send("logname=" + logname);
    }
}

function check_confirmlogpass()
{
    logpass = document.getElementById("logpass");
    confirmlog = document.getElementById("confirmlogpass");
    confirmicon = document.getElementById("logpass_confirm_icon");
    submit_btn = document.getElementById("submit_button");
    if (confirmlog.value === logpass.value)
    {
        confirmicon.style.visibility = "hidden";
        submit_btn.disabled = false;
    }

    else
    {
        confirmicon.style.visibility = "visible";
        submit_btn.disabled = true;
    }
}