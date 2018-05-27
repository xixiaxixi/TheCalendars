function course_data_import(user_id,StudentID, VPNPassword, RegistryDepartmentPassword)
{
    $.post('ajax/DeleteProj.php',{username:user_id,zfname:StudentID,zfpswd:RegistryDepartmentPassword},
           function (data)
           {
               data=JSON.parse(data);
               if (data.state!=='ok')
               {
                   alert(data);
                   location.reload();
               }
               else
               {
                   alert('输入成功!');
                   location.replace('home.php');
               }
           });
}

function check_personal_info()
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
            Name = document.getElementById('Name');
            StudentID = document.getElementById('StudentID');
            VPNPassword = document.getElementById('VPNPassword');
            RegistryDepartmentPassword = document.getElementById('RegistryDepartmentPassword');
            delete_button = document.getElementById('delete_button');
            submit_button = document.getElementById('submit_button');
            if (xmlhttp.responseText.indexOf('null') === -1)
            {
                var personalinfo = JSON.parse(xmlhttp.responseText);
                Name.value = personalinfo[0];
                StudentID.value = personalinfo[1];
                VPNPassword.value = personalinfo[2];
                RegistryDepartmentPassword.value = personalinfo[3];
                submit_button.style.display = 'none';
                delete_button.style.display = 'block';
            }
            else
            {
                Name.value = '';
                StudentID.value = '';
                VPNPassword.value = '';
                RegistryDepartmentPassword.value = '';
                submit_button.style.display = 'block';
                delete_button.style.display = 'none';
            }
        }
    };
    xmlhttp.open("GET", "ajax/personal_info_check.php", true);
    xmlhttp.send();
}

function delete_personalinfo()
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
            check_personal_info();
        }
    };
    xmlhttp.open("GET", "ajax/personal_info_delete.php", true);
    xmlhttp.send();
}