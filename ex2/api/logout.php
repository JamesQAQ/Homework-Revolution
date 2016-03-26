<?php
  include "config.php";

  /*
  Input:
    NULL
  Output:
    {"status":"success"}
  */

  if ($USER != NULL){
    $login_time_limit = time() - COOKIE_TIME;
    $SessionID = USER_SESSIONS;
    
    $stmt = mysqli_prepare($MYSQLI, "DELETE FROM `Sessions` WHERE `SessionID` = ? OR `LoginTime` <= ?");
    mysqli_stmt_bind_param($stmt, "si", $SessionID, $login_time_limit);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    setcookie("PHPSESSID", "", time() - 3600, "/"); // delete session cookie  

    echo json_encode(array("status"=>"success"));
  }
  else{
    echo json_encode(array("status"=>"not login"));
  }

?>