<?php
  include "../config.php";

  if ($USER == NULL){
    redirect("/login.php");
    exit();
  }

  if (isset($_POST['rid']) && isset($_POST['description'])){
    $stmt = mysqli_prepare($MYSQLI, "INSERT INTO `RequestResponse` (`rid`, `username`, `description`) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iss", $_POST['rid'], $USER['username'], $_POST['description']);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    echo json_encode(array("status"=>"success"));
  }
?>
