<?php

  function ck_login()
  {
    global $MYSQLI;
    $SessionID = USER_SESSIONS;
    $stmt = mysqli_prepare($MYSQLI, "SELECT `username`, `LoginTime` FROM `Sessions` WHERE `SessionID` = ?");
    $stmt->bind_param("s", $SessionID);
    my_mysqli_stmt_execute($stmt);
    $stmt->bind_result($username, $LoginTime);
    $stmt->fetch();
    $stmt->close();

    if ($LoginTime >= (time() - COOKIE_TIME)){
      $res = mysqli_query($MYSQLI, "SELECT `username`, `groupname` FROM `radusergroup` WHERE `username` = '".mysqli_real_escape_string($MYSQLI, $username)."'");
      $res = mysqli_fetch_array($res);
      $res[2] = $res['groupadmin'] = is_groupadmin($username, $res['groupname']);
      return $res;
    }
    else{
      return NULL;
    }
  }

  function my_mysqli_stmt_execute($stmt){
    if (!mysqli_stmt_execute($stmt)){
      echo json_encode(array("status"=>mysqli_stmt_error($stmt)));
      exit();
    }
  }

  function is_groupadmin($username, $groupname){
    global $MYSQLI;
    $stmt = mysqli_prepare($MYSQLI, "SELECT 1 FROM `GroupAdmins` WHERE `username` = ? AND `groupname` = ?");
    mysqli_stmt_bind_param($stmt, "ss", $username, $groupname);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $tmp);
    if (mysqli_stmt_fetch($stmt)){
      mysqli_stmt_close($stmt);
      return true;
    }
    mysqli_stmt_close($stmt);
    return false;
  }

  function is_ingroup($username, $groupname){
    global $MYSQLI;
    $stmt = mysqli_prepare($MYSQLI, "SELECT 1 FROM `radusergroup` WHERE `username` = ? AND `groupname` = ?");
    mysqli_stmt_bind_param($stmt, "ss", $username, $groupname);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $tmp);
    if (mysqli_stmt_fetch($stmt)){
      mysqli_stmt_close($stmt);
      return true;
    }
    mysqli_stmt_close($stmt);
    return false;
  }

  function check_traffic_limit($username){
    global $MYSQLI;
    $stmt = mysqli_prepare($MYSQLI, "SELECT SUM(`acctinputoctets` + `acctoutputoctets`) FROM `radacct` WHERE `username` = ? AND date_format(`acctstarttime`, '%Y-%m-%d') = date_format(now(),'%Y-%m-%d')");
    mysqli_stmt_bind_param($stmt, "s", $username);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $traffic);
    if (mysqli_stmt_fetch($stmt)){
      if ($traffic == NULL)
          $traffic = 0;
    }
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($MYSQLI, "SELECT `TrafficLimit` FROM `Limits` WHERE `username` = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $traffic_limit);
    if (!mysqli_stmt_fetch($stmt)){
      mysqli_stmt_close($stmt);
      return true;
    }
    mysqli_stmt_close($stmt);

    return $traffic <= $traffic_limit;
  }

  function check_time_limit($username){
    global $MYSQLI;
    $stmt = mysqli_prepare($MYSQLI, "SELECT SUM(`acctsessiontime`) FROM `radacct` WHERE `username` = ? AND date_format(`acctstarttime`, '%Y-%m-%d') = date_format(now(),'%Y-%m-%d')");
    mysqli_stmt_bind_param($stmt, "s", $username);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $time);
    if (mysqli_stmt_fetch($stmt)){
      if ($time == NULL)
          $time = 0;
    }
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($MYSQLI, "SELECT `TimeLimit` FROM `Limits` WHERE `username` = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $time_limit);
    if (!mysqli_stmt_fetch($stmt)){
      mysqli_stmt_close($stmt);
      return true;
    }
    mysqli_stmt_close($stmt);

    return $time <= $time_limit;
  }

  function uam_disconnect($username){
    exec("echo User-Name=".$username." | radclient -x ".UAM_IP." disconnect ".RADIUS_SECRET);
  }

  function redirect($url){
    echo '<script>window.location="'.$url.'"</script>';
  }

?>