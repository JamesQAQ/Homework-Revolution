<?php
  include "../config.php";

  /*
  Input:
    groupname=family
    &regular=1
  Output:
    {
      "status":"success"
    }
  */

  if (isset($_POST['groupname']) && isset($_POST['regular'])){
    if ($USER['groupname'] === "admin" || is_groupadmin($USER['username'], $_POST['groupname'])){
      $regular = intval($_POST['regular']);  

      $stmt = mysqli_prepare($MYSQLI, "UPDATE `Options` SET `regular` = ? WHERE `groupname` = ?");
      mysqli_stmt_bind_param($stmt, "is", $regular, $_POST['groupname']);
      my_mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);

      echo json_encode(array("status"=>"success"));
    }
    else{
      echo json_encode(array("status"=>"permission denied"));
    }
  }

?>
