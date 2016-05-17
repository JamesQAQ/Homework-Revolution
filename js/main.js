function traffic_string(traffic){
  var size = [1024 * 1024 * 1024, 1024 * 1024, 1024, 1];
  var unit = ["GB", "MB", "KB", "B"];
  for (var i = 0; i < size.length; i++){
    if (traffic >= size[i]){
      return (traffic / size[i]).toFixed(2) + " " + unit[i];
    }
  }
  return "0 B";
}

function time_string(time){
  var size = [24 * 60 * 60, 60 * 60, 60, 1];
  var unit = ["日", "小時", "分", "秒"];
  for (var i = 0; i < size.length; i++){
    if (time >= size[i]){
      var res = Math.floor(time / size[i]) + " " + unit[i];
      time %= size[i];
      if (i + 1 < size.length){
        res += " ";
        res += Math.floor(time / size[i + 1]) + " " + unit[i + 1];
      }
      return res;
    }
  }
  return "0 秒";
}

function get_traffic(username){
  if (username != undefined){
    $.ajax({
      type: "GET",
      url: "/api/get_traffic.php?username=" + username,
      cache: false,
      dataType: "json",
      success: function(res)
      {
        td_list = $('table.traffic-table td');
        td_list[0].innerText = res.username;
        td_list[1].innerText = traffic_string(res.input);
        td_list[2].innerText = traffic_string(res.output);
        td_list[3].innerText = traffic_string(res.total);
        td_list[4].innerText = time_string(res.time);
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
            td_list[1].innerText = traffic_string(res2[username].input);
            td_list[2].innerText = traffic_string(res2[username].output);
            td_list[3].innerText = traffic_string(res2[username].total);
            td_list[4].innerText = time_string(res2[username].time);
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
        Materialize.toast('帳號名稱或密碼錯誤！', 4000);
    },
    error: function (xhr, ajaxOptions, thrownError)
    {
      console.log(xhr);
      Materialize.toast('系統錯誤，請聯絡管理員。', 4000);
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
      window.location = '/login.php';
    },
    error: function (xhr, ajaxOptions, thrownError)
    {
      console.log(xhr);
      Materialize.toast('系統錯誤，請聯絡管理員。', 4000);
    }
  });
}

function register(){
  var username = $("#reg-username").val();
  var password = $("#reg-password").val();
  $.ajax({
    type: "POST",
    url: "/api/register.php",
    cache: false,
    async: false,
    data:{
          "username": username,
          "password": password,
         },
    dataType: "json",
    success: function(res)
    {
      if (res.status == "success"){
        Materialize.toast('註冊成功！', 4000);
        $('#modal-add').closeModal();
      }
      else if (res.status == "username existed")
        Materialize.toast('此帳號已被註冊。', 4000);
    },
    error: function (xhr, ajaxOptions, thrownError)
    {
      console.log(xhr);
      Materialize.toast('系統錯誤，請聯絡管理員。', 4000);
    }
  });
}

function process_limit(td){
  var tmp = td.innerText.split(" ");
  var input = $("<input>");
  input.addClass("origin-input");
  input.attr("type", "number");
  input.attr("value", tmp[0]);
  $(td).html("");
  $(td).append(input);
  $(td).append(" " + tmp[1]);
}

function edit_limit(btn){
  var traffic_limit_td = $(btn).parent().parent().children('td')[5];
  var time_limit_td = $(btn).parent().parent().children('td')[6];
  var default_time_limit_td = undefined;
  if ($(btn).parent().parent().children('td').length == 9)
    default_time_limit_td = $(btn).parent().parent().children('td')[7];

  process_limit(traffic_limit_td);
  process_limit(time_limit_td);
  if (default_time_limit_td != undefined)
    process_limit(default_time_limit_td);

  $(btn).attr("onclick", "javascript:update_limit(this)");
  $(btn).html("送出");
}

function update_limit(btn){
  var username = $(btn).parent().parent().children('td')[0].innerText;
  var traffic_limit = $($(btn).parent().parent().children('td')[5]).children("input").val();
  var time_limit = $($(btn).parent().parent().children('td')[6]).children("input").val();
  var default_time_limit = undefined;
  if ($(btn).parent().parent().children('td').length == 9)
    default_time_limit = $($(btn).parent().parent().children('td')[7]).children("input").val();

  var limit_data = {
    "username": username,
    "traffic": traffic_limit,
    "time": time_limit
  };
  if (default_time_limit != undefined)
    limit_data['default'] = default_time_limit;

  $.ajax({
    type: "POST",
    url: "/api/update_limit.php",
    cache: false,
    async: false,
    data: limit_data,
    dataType: "json",
    success: function(res)
    {
      location.reload();
    },
    error: function (xhr, ajaxOptions, thrownError)
    {
      console.log(xhr);
      Materialize.toast('系統錯誤，請聯絡管理員。', 4000);
    }
  });
}

function update_regular(val, groupname){
  $.ajax({
    type: "POST",
    url: "/api/update_regular.php",
    cache: false,
    async: false,
    data: {
      "regular": val,
      "groupname": groupname
    },
    dataType: "json",
    success: function(res)
    {
      if (res.status == 'success')
        location.reload();
      else
        Materialize.toast('權限不足。', 4000);
    },
    error: function (xhr, ajaxOptions, thrownError)
    {
      console.log(xhr);
      Materialize.toast('系統錯誤，請聯絡管理員。', 4000);
    }
  });
}

var egg_string = "aaaaaaaaa";

function show_egg(){
  $("body").css("background-size", "cover");
  $("body").css("background-image", "url('http://www.csie.ntu.edu.tw/~b01902112/yaranaika.jpg')");
  var audio = $("<audio>")[0];
  audio.src = "http://www.csie.ntu.edu.tw/~b01902112/yaranaika.mp3";
  audio.play();
  audio.volume = 0.4;
}

window.addEventListener('keydown', function(e){
  egg_string = egg_string.slice(1) + String.fromCharCode(e.keyCode);
  if (egg_string == "YARANAIKA"){
    show_egg();
  }
});