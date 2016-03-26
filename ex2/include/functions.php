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
      return mysqli_fetch_array($res);
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

?>