$(document).ready(function ()
                  {
                      $('#search_button').on('click', function ()
                      {
                          var search_id = $(this).prev('#Name').val();
                          var already_exist=0;
                          $("input[type='checkbox'][name='member[]']").each(function ()
                                                                            {
                                                                                var checkbox=$(this);
                                                                                if(checkbox.val()===search_id)
                                                                                {
                                                                                    already_exist=1;
                                                                                    checkbox.attr('checked','checked');
                                                                                }
                                                                            });
                          if(!already_exist)
                          {
                              var search_div=$(this).parent();
                              $.post('../ajax/search_a_person.php', {search_id: search_id},
                                     function (data)
                                     {
                                         if (data.includes("error"))
                                         {
                                             alert(data);
                                         }
                                         else
                                         {
                                             var add_checkbox = "<input type='checkbox' name='member[]' value='" + search_id + "' id='" + search_id + "' checked='checked'/>";
                                             add_checkbox += "<label for='" + search_id + "'>"+data+"</label><br>";
                                             search_div.before(add_checkbox);
                                             swiper.update();
                                         }
                                     });
                          }
                      })
                  });