<?php
  include "config.php";

  if (isset($_GET['username']))
  {
    $stmt = mysqli_prepare($MYSQLI, "SELECT SUM(`acctinputoctets` + `acctoutputoctets`) FROM `radacct` WHERE `username` = ? AND date_format(`acctstarttime`, '%Y-%m-%d') = date_format(now(),'%Y-%m-%d')");
    mysqli_stmt_bind_param($stmt, "s", $_GET['username']);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $traffic);
    if (mysqli_stmt_fetch($stmt)){
      echo json_encode(array("traffic"=>$traffic));
    }
    else{
      echo json_encode(array("status"=>"error"));
    }
    mysqli_stmt_close($stmt);

    
  }
?>
