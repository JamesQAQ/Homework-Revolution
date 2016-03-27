<?php
  include "../config.php";

  $stmt = mysqli_prepare($MYSQLI, "SELECT `username`, SUM(`acctinputoctets`), SUM(`acctoutputoctets`), SUM(`acctsessiontime`) FROM `radacct` WHERE date_format(`acctstarttime`, '%Y-%m-%d') = date_format(now(),'%Y-%m-%d') GROUP BY `username`");
  my_mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt, $username, $input, $output, $time);

  $res = array();

  while (mysqli_stmt_fetch($stmt)){
    $tmp = array();
    if ($input == NULL)
        $input = 0;
    if ($output == NULL)
        $output = 0;
    $tmp['username'] = $username;
    $tmp['input'] = $input;
    $tmp['output'] = $output;
    $tmp['total'] = $input + $output;
    $tmp['time'] = $time;
    array_push($res, $tmp);
  }
  mysqli_stmt_close($stmt);

  echo json_encode($res); 
?>
