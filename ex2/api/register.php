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

  if (isset($_POST['username']) && isset($_POST['password'])){

    $stmt = mysqli_prepare($MYSQLI, "SELECT `id` FROM `radcheck` WHERE `username` = ?");
    mysqli_stmt_bind_param($stmt, "s", $_POST['username']);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id);
    if (mysqli_stmt_fetch($stmt)){
      echo json_encode(array("status"=>"username existed"));
      exit();
    }
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($MYSQLI, "INSERT INTO `radcheck` (`username`, `attribute`, `op`, `value`) VALUES (?, 'Cleartext-Password', ':=', ?)");
    mysqli_stmt_bind_param($stmt, "ss", $_POST['username'], $_POST['password']);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($MYSQLI, "INSERT INTO `radusergroup` (`username`, `groupname`) VALUES (?, 'user')");
    mysqli_stmt_bind_param($stmt, "s", $_POST['username']);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    echo json_encode(array("status"=>"success")); // "status"=>"success" 
  }

?>
