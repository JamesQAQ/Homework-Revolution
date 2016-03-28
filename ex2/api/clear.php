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
    $res = array();
    $stmt = mysqli_prepare($MYSQLI, "SELECT `username` FROM `radcheck` WHERE `attribute` = 'Cleartext-Password'");
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $username);
    while (mysqli_stmt_fetch($stmt)){
      array_push($user_list, $username);
    }
    mysqli_stmt_close($stmt);
    foreach ($user_list as $username){
      if (!(check_traffic_limit($username) && check_time_limit($username))){
        uam_disconnect($username);
        array_push($res, $username);
      }
    }

    $stmt = mysqli_prepare($MYSQLI, "SELECT a.`username` FROM `radacct` a, `radusergroup` b WHERE a.`username`=b.`username` AND b.`groupname`='user' AND a.`acctstoptime` IS NULL AND date_format(a.`acctstarttime`, '%Y-%m-%d') < date_format(now(),'%Y-%m-%d')");
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $username);
    while (mysqli_stmt_fetch($stmt)){
      uam_disconnect($username);
      array_push($res, $username);
    }
    mysqli_stmt_close($stmt);

    echo json_encode($res);
  }
  else{
    echo json_encode(array("status"=>"wrong"));
  }
?>