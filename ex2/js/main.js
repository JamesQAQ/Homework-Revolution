function get_traffic(username){
  if (username != undefined){
    $.ajax({
      type: "GET",
      url: "/api/get_traffic.php?username=" + username,
      cache: false,
      dataType: "json",
      success: function(res)
      {
        console.log(res);
        td_list = $('table.traffic-table td');
        td_list[0].innerText = res.username;
        td_list[1].innerText = res.input;
        td_list[2].innerText = res.output;
        td_list[3].innerText = res.total;
        td_list[4].innerText = res.time;
      },
      error: function (xhr, ajaxOptions, thrownError)
      {
        console.log(xhr);
      }
    });
  }
  else{
    $.ajax({
      type: "GET",
      url: "/api/get_all_traffic.php",
      cache: false,
      dataType: "json",
      success: function(res)
      {
        console.log(res);

        res2 = {};
        for (var i = 0; i < res.length; i++){
          res2[res[i].username] = {};
          res2[res[i].username].input = res[i].input;
          res2[res[i].username].output = res[i].output;
          res2[res[i].username].total = res[i].total;
          res2[res[i].username].time = res[i].time;
        }

        tr_list = $('table.traffic-table tr');
        for (var i = 1; i < tr_list.size(); i++){
          var td_list = $(tr_list[i]).children("td");
          var username = td_list[0].innerText;
          if (res2[username] != undefined){
            td_list[1].innerText = res2[username].input;
            td_list[2].innerText = res2[username].output;
            td_list[3].innerText = res2[username].total;
            td_list[4].innerText = res2[username].time;
          }
        }
      },
      error: function (xhr, ajaxOptions, thrownError)
      {
        console.log(xhr);
      }
    });
  }
}

function login(username, password){
  $.ajax({
    type: "POST",
    url: "/api/login.php",
    cache: false,
    async: false,
    data:{
          "username": username,
          "password": password,
         },
    dataType: "json",
    success: function(res)
    {
      if (res.status == "success")
        location.reload();
      else if (res.status == "failed")
        Materialize.toast('帳號名稱或密碼錯誤！', 4000)
    },
    error: function (xhr, ajaxOptions, thrownError)
    {
      console.log(xhr);
      Materialize.toast('系統錯誤，請聯絡管理員。', 4000)
    }
  });
}

function logout(){
  $.ajax({
    type: "GET",
    url: "/api/logout.php",
    cache: false,
    async: false,
    dataType: "json",
    success: function(res)
    {
      location.reload();
    },
    error: function (xhr, ajaxOptions, thrownError)
    {
      console.log(xhr);
      Materialize.toast('系統錯誤，請聯絡管理員。', 4000)
    }
  });
}