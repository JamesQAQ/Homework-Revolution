<?php
  include "config.php";

  if ($USER == NULL){
    redirect("/login.php");
    exit();
  }

  if (isset($_GET['id'])){
    $stmt = mysqli_prepare($MYSQLI, "SELECT `data` FROM `RequestPhoto` WHERE `id` = ?");
    mysqli_stmt_bind_param($stmt, "i", $_GET['id']);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    mysqli_stmt_bind_result($stmt, $data);
    if (!mysqli_stmt_fetch($stmt)){
      echo json_encode(array("status"=>"request not exists"));
      exit();
    }
    mysqli_stmt_close($stmt);

    header('Content-type: image');
    echo base64_decode($data);
  }
?>
