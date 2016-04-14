<?php
  include "../config.php";

  /*
  Input:
    username=test
    &password=test
  Output:
    {
      "status":"success"
    }
  */

  if (isset($_POST['username']) && isset($_POST['password']))
  {
    $username = $_POST['username'];

    $stmt = mysqli_prepare($MYSQLI, "SELECT `value` FROM `radcheck` WHERE `attribute` = 'Cleartext-Password' AND `username` = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $password);
    if (!mysqli_stmt_fetch($stmt)){
      echo json_encode(array("status"=>"failed"));
      exit();
    }
    mysqli_stmt_close($stmt);

    if ($password === $_POST['password']){

      $login_time_limit = time() - COOKIE_TIME;
      $SessionID = USER_SESSIONS;
      
      $stmt = mysqli_prepare($MYSQLI, "DELETE FROM `Sessions` WHERE `SessionID` = ? OR LoginTime <= ?");
      mysqli_stmt_bind_param($stmt, "si", $SessionID, $login_time_limit);
      my_mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);

      $LoginTime = time();
      $stmt = mysqli_prepare($MYSQLI, "INSERT INTO Sessions(SessionID, username, LoginTime) VALUES(?, ?, ?)");
      mysqli_stmt_bind_param($stmt, "ssi", $SessionID, $username, $LoginTime);
      my_mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);

      echo json_encode(array("status"=>"success"));
    }
    else{
      echo json_encode(array("status"=>"failed"));
    }
  }
?>