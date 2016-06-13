<?php
  include "config.php";

  if ($USER == NULL){
    redirect("/login.php");
    exit();
  }  
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Homework Revolution 管理平台</title>
    <link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="css/main.css">
    <script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="js/materialize.min.js"></script>
    <script type="text/javascript" src="js/main.js"></script>
  </head>
  <body>
      <div class="container">
        <div class="row center">
          <h2 class="blue-text">Homework Revolution</h2>
        </div>
        <?php
          if ($USER['groupname'] === 'admin')
            display_manage();
          else
            echo json_encode(array("status"=>"permission denied"));
        ?>
      </div>
  </body>
</html>

<?php
  function display_manage(){
    global $MYSQLI;
?>
    <div class="row center">
      <h4>帳號管理</h4>
      <?php include "bar.php"; ?>
      <br>
      <div class="row">
        <div class="input-field col offset-s1 s2">
          <input id="new-groupname" type="text" class="validate">
          <label for="new-groupname">新群組名稱</label>
        </div>
        <a class="waves-effect waves-light btn col s1" href="javascript:create_group()">新增</a>
        <a class="waves-effect waves-light btn col offset-s5 s2 create-user" href="#modal1">新增帳號</a>
      </div>
      <style>
      <!--
        .btn{
          margin-top: 18px;
        }
      -->
      </style>
      <div class="row">        
        <div class="col offset-s1 s3">
          <table class="traffic-table centered striped">
            <thead>
              <th style="width: 100%;">現有群組名稱</th>
            </thead>
            <tbody>
              <?php
                $stmt = mysqli_prepare($MYSQLI, "SELECT `groupname` FROM `radgroupreply` GROUP BY `groupname` ORDER BY `groupname`");
                my_mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $groupname);
                while (mysqli_stmt_fetch($stmt)){
                  echo '<tr><td>'.$groupname.'</td></tr>';
                }
                mysqli_stmt_close($stmt);
              ?>
            </tbody>
          </table>
        </div>
        <div class="col offset-s1 s6">
          <table class="traffic-table centered striped">
            <thead>
              <th style="width: 50%;">群組名稱</th>
              <th style="width: 50%;">帳號名稱</th>
            </thead>
            <tbody>
              <?php
                $stmt = mysqli_prepare($MYSQLI, "SELECT `username`, `groupname` FROM `radusergroup` ORDER BY `groupname`, `username`");
                my_mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $username, $groupname);
                while (mysqli_stmt_fetch($stmt)){
                  echo '<tr><td>'.$groupname.'</td>';
                  echo '<td>'.$username.'</td></tr>';
                }
                mysqli_stmt_close($stmt);
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
<?php
  }
?>

<!-- for creating new user -->
<div id="modal1" class="modal">
  <div class="modal-content row">
    <div class="input-field col s6">
      <input id="username" type="text" class="validate">
      <label for="username">帳號名稱</label>
    </div>
    <div class="input-field col s6">
      <input id="groupname" type="text" class="validate">
      <label for="groupname">群組名稱</label>
    </div>
    <div class="input-field col s6">
      <input id="password" type="password" class="validate">
      <label for="password">密碼</label>
    </div>
    <div class="input-field col s6">
      <input id="password2" type="password" class="validate">
      <label for="password2">重覆輸入密碼</label>
    </div>
    <div class="input-field col s6">
      <input id="email" type="email" class="validate">
      <label for="email">電子信箱</label>
    </div>
    <div style="margin-left: 10px;">
      管理者： 
      <input class="with-gap" name="group1" type="radio" id="admin_on"  />
      <label for="admin_on">Yes</label>
      &nbsp;&nbsp;&nbsp;
      <input class="with-gap" name="group1" type="radio" id="admin_off"  />
      <label for="admin_off">No</label>
    </div>

    <div class="modal-footer">
      <a class=" modal-action modal-close waves-effect waves-green btn-flat" href="javascript:create_user()">送出</a>
    </div>
</div>
<script>
  $('.create-user').leanModal();
  $('#admin_off')[0].checked = true;
</script>