<?php
  include "../config.php";

  /*
  Input:
    groupname=family
    &username=test
    &password=test
    &admin=
  Output:
    {
      "status":"success"
    }
  */

  if ($USER['groupname'] === 'admin' && isset($_POST['groupname'])
      && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email'])){

    $stmt = mysqli_prepare($MYSQLI, "SELECT `id` FROM `radcheck` WHERE `username` = ?");
    mysqli_stmt_bind_param($stmt, "s", $_POST['username']);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id);
    if (mysqli_stmt_fetch($stmt)){
      echo json_encode(array("status"=>"username existed"));
      exit();
    }
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($MYSQLI, "SELECT `id` FROM `radgroupreply` WHERE `groupname` = ?");
    mysqli_stmt_bind_param($stmt, "s", $_POST['groupname']);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id);
    if (!mysqli_stmt_fetch($stmt)){
      echo json_encode(array("status"=>"group not exists"));
      exit();
    }
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($MYSQLI, "INSERT INTO `radcheck` (`username`, `attribute`, `op`, `value`) VALUES (?, 'Cleartext-Password', ':=', ?)");
    mysqli_stmt_bind_param($stmt, "ss", $_POST['username'], $_POST['password']);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($MYSQLI, "INSERT INTO `radusergroup` (`username`, `groupname`) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ss", $_POST['username'], $_POST['groupname']);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($MYSQLI, "INSERT INTO `Limits` (`username`, `TrafficLimit`, `TimeLimit`) VALUES (?, 536870912, 1800)");
    mysqli_stmt_bind_param($stmt, "s", $_POST['username']);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($MYSQLI, "INSERT INTO `Emails` (`username`, `email`) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ss", $_POST['username'], $_POST['email']);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if (isset($_POST['admin'])){
      $stmt = mysqli_prepare($MYSQLI, "INSERT INTO `GroupAdmins` (`username`, `groupname`) VALUES (?, ?)");
      mysqli_stmt_bind_param($stmt, "ss", $_POST['username'], $_POST['groupname']);
      my_mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);
    }

    echo json_encode(array("status"=>"success"));
  }
  else{
    echo json_encode(array("status"=>"permission denied"));
  }

?>
