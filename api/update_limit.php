<?php
  include "../config.php";

  /*
  Input:
    username=test
    &traffic=128
    &time=60
  Output:
    {
      "status":"success"
    }
  */

  if ($USER['groupname'] !== "admin"){
    echo json_encode(array("status"=>"permission denied"));
  }
  else if (isset($_POST['username']) && isset($_POST['traffic']) && isset($_POST['time'])){
    $time_limit = intval($_POST['time']) * 60;
    $traffic_limit = intval($_POST['traffic']) * 1024 * 1024;
    
    $stmt = mysqli_prepare($MYSQLI, "UPDATE `Limits` SET `TimeLimit` = ?, `TrafficLimit` = ? WHERE `username` = ?");
    mysqli_stmt_bind_param($stmt, "iis", $time_limit, $traffic_limit, $_POST['username']);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    echo json_encode(array("status"=>"success"));
  }

?>