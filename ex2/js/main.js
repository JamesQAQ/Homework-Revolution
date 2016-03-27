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
}