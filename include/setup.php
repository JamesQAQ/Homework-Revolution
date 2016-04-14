<?PHP

  $MYSQLI = mysqli_connect(preg_split('/:/', DB_HOST)[0], DB_USER, DB_PASS, DB_NAME, preg_split('/:/', DB_HOST)[1]);

  if (mysqli_connect_errno()){
    echo json_encode(array("status"=>mysqli_connect_errno()));
    exit();
  }

  mysqli_set_charset($MYSQLI, "utf8");

  $USER = ck_login();

?>