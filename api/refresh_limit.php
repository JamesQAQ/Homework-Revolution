<?php
  include "../config.php";

  /*
  Input:
    secret=testing123
  Output:
    {
      "status":"success"
    }
  */

  if (isset($_POST['secret']) && $_POST['secret'] === RADIUS_SECRET){
    $user_list = array();
    $group_list = array();
    $stmt = mysqli_prepare($MYSQLI, "SELECT `username`, `groupname` FROM `radusergroup`");
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $username, $groupname);
    while (mysqli_stmt_fetch($stmt)){
      array_push($user_list, $username);
      array_push($group_list, $groupname);
    }
    mysqli_stmt_close($stmt);
    
    for ($i = 0; $i < sizeof($user_list); $i++){
      $username = $user_list[$i];
      $groupname = $group_list[$i];
      if ($groupname !== 'admin' && !is_groupadmin($username, $groupname)){
        if (is_regular_on($groupname)){
          $stmt = mysqli_prepare($MYSQLI, "SELECT `DefaultTimeLimit` FROM `Limits` WHERE `username` = ?");
          mysqli_stmt_bind_param($stmt, "s", $username);
          my_mysqli_stmt_execute($stmt);
          mysqli_stmt_bind_result($stmt, $DefaultTimeLimit);
          if (!mysqli_stmt_fetch($stmt))
            continue;
          mysqli_stmt_close($stmt);

          $stmt = mysqli_prepare($MYSQLI, "UPDATE `Limits` SET `TimeLimit` = ? WHERE `username` = ?");
          mysqli_stmt_bind_param($stmt, "is", $DefaultTimeLimit, $username);
          my_mysqli_stmt_execute($stmt);
          mysqli_stmt_close($stmt);
        }
        else{
          $stmt = mysqli_prepare($MYSQLI, "SELECT `TimeLimit` FROM `Limits` WHERE `username` = ?");
          mysqli_stmt_bind_param($stmt, "s", $username);
          my_mysqli_stmt_execute($stmt);
          mysqli_stmt_bind_result($stmt, $TimeLimit);
          if (!mysqli_stmt_fetch($stmt))
            continue;
          mysqli_stmt_close($stmt);

          $stmt = mysqli_prepare($MYSQLI, "SELECT SUM(`acctsessiontime`) FROM `radacct` WHERE `username` = ? AND date_format(`acctstarttime`, '%Y-%m-%d') = date_format(NOW( ) - INTERVAL 1 DAY,'%Y-%m-%d') GROUP BY `username`");
          mysqli_stmt_bind_param($stmt, "s", $username);
          my_mysqli_stmt_execute($stmt);
          mysqli_stmt_bind_result($stmt, $last_day_time);
          if (!mysqli_stmt_fetch($stmt))
            $last_day_time = 0;
          mysqli_stmt_close($stmt);

          $stmt = mysqli_prepare($MYSQLI, "UPDATE `Limits` SET `TimeLimit` = ? WHERE `username` = ?");
          mysqli_stmt_bind_param($stmt, "is", max($TimeLimit - $last_day_time, 0), $username);
          my_mysqli_stmt_execute($stmt);
          mysqli_stmt_close($stmt);
        }
      }
    }
    echo json_encode(array("status"=>"success"));
  }
  else{
    echo json_encode(array("status"=>"wrong"));
  }
?>