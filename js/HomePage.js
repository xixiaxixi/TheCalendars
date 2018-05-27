$(document).ready(function ()
                  {
                     $('.delete-button').on('click',function ()
                     {
                         var proj_div = $(this).closest(".proj-display");
                         var proj_title=proj_div.find(".proj-name").text();
                         if(!confirm('Delete the Project '+proj_title+'?'))return;
                         var proj_id=$(this).attr('id');
                         $.post('ajax/DeleteProj.php',{proj_id:proj_id},
                                function (data)
                                {
                                    if (data.includes("error"))
                                    {
                                        alert(data);
                                        location.reload();
                                    }
                                    else
                                    {
                                        proj_div.remove();
                                    }
                                })
                     })
                  });
