<?php
  include "../config.php";

  /*
  Input:
    groupname=family
  Output:
    {
      "status":"success"
    }
  */

  if ($USER['groupname'] === 'admin' && isset($_POST['groupname'])){

    $stmt = mysqli_prepare($MYSQLI, "SELECT `id` FROM `radgroupreply` WHERE `groupname` = ?");
    mysqli_stmt_bind_param($stmt, "s", $_POST['groupname']);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id);
    if (mysqli_stmt_fetch($stmt)){
      echo json_encode(array("status"=>"groupname existed"));
      exit();
    }
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($MYSQLI, "INSERT INTO `radgroupreply` (`groupname`, `attribute`, `op`, `value`) VALUES (?,'Auth-Type',':=','CHAP')");
    mysqli_stmt_bind_param($stmt, "s", $_POST['groupname']);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($MYSQLI, "INSERT INTO `radgroupreply` (`groupname`, `attribute`, `op`, `value`) VALUES (?,'Service-Type',':=','Framed-User')");
    mysqli_stmt_bind_param($stmt, "s", $_POST['groupname']);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($MYSQLI, "INSERT INTO `radgroupreply` (`groupname`, `attribute`, `op`, `value`) VALUES (?,'Acct-Interim-Interval',':=','60')");
    mysqli_stmt_bind_param($stmt, "s", $_POST['groupname']);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($MYSQLI, "INSERT INTO `radgroupreply` (`groupname`, `attribute`, `op`, `value`) VALUES (?,'Session-Timeout',':=','3600')");
    mysqli_stmt_bind_param($stmt, "s", $_POST['groupname']);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($MYSQLI, "INSERT INTO `radgroupreply` (`groupname`, `attribute`, `op`, `value`) VALUES (?,'Idle-Timeout',':=','600')");
    mysqli_stmt_bind_param($stmt, "s", $_POST['groupname']);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($MYSQLI, "INSERT INTO `radgroupcheck` (`groupname`, `attribute`, `op`, `value`) VALUES (?, 'Simultaneous-Use', ':=', '1')");
    mysqli_stmt_bind_param($stmt, "s", $_POST['groupname']);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    echo json_encode(array("status"=>"success"));
  }
  else{
    echo json_encode(array("status"=>"permission denied"));
  }

?>
