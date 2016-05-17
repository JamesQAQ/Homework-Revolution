<?php
  include "config.php";

  if ($USER == NULL){
    redirect("/login.php");
    exit();
  }

  if ($_POST != NULL && $_FILES != NULL){
    $stmt = mysqli_prepare($MYSQLI, "INSERT INTO `Request` (`username`, `description`) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ss", $USER['username'], $_POST['description']);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $rid = mysqli_insert_id($MYSQLI);
    $photos = $_FILES['photos'];
    $size = sizeof($photos['name']);
    for ($i = 0; $i < $size; $i++){
      $data = base64_encode(file_get_contents($photos['tmp_name'][$i]));

      $stmt = mysqli_prepare($MYSQLI, "INSERT INTO `RequestPhoto` (`rid`, `data`) VALUES (?, ?)");
      mysqli_stmt_bind_param($stmt, "is", $rid, $data);
      my_mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);
    }
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
          if ($USER['groupname'] === 'admin' or is_groupadmin($USER['username'], $USER['groupname']))
            display_requests();
          else
            submit_request();
        ?>
      </div>
  </body>
</html>

<?php
  function submit_request(){
?>
    <div class="row center">
      <h4>請求列表</h4>
      <a href="javascript:logout()">
        登出
      </a>
    </div>
    
    <div class="row center">
      <!-- Modal Trigger -->
      <a class="waves-effect waves-light btn modal-trigger" href="#modal1">新增</a>
      <br><br>
      <table class="traffic-table centered striped">
        <thead>
          <tr>
              <th style="width: 20%;">上傳時間</th>
              <th style="width: 50%;">敘述</th>
              <th style="width: 15%;">狀態</th>
              <th style="width: 15%;">詳細</th>
          </tr>
        </thead>
        <tbody>
          <?php
            global $MYSQLI, $USER;
            $stmt = mysqli_prepare($MYSQLI, "SELECT a.`date`, a.`description`, a.`read` FROM `Request` a, `radusergroup` b WHERE a.`username`=b.`username` AND a.`username`=? ORDER BY a.`date` DESC");
            mysqli_stmt_bind_param($stmt, "s", $USER['username']);
            my_mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $date, $description, $read);
            while (mysqli_stmt_fetch($stmt)){
              echo '<tr>';
                echo '<td>'.$date.'</td>';
                echo '<td style="text-align: left;">'.$description.'</td>';
                echo '<td >';
                  if ($read === 1)
                    echo '<font color="green">已讀</font>';
                  else
                    echo '<font color="red">未讀</font>';
                echo '</td>';
                echo '<td>詳細</td>';
              echo '</tr>';
            }
            mysqli_stmt_close($stmt);
          ?>
        </tbody>
      </table>
    </div>

    <!-- Modal Structure -->
    <div id="modal1" class="modal">
      <div class="modal-content">
        <h4>新增請求</h4>
        <form action="/request.php" method="post" enctype="multipart/form-data">
          <div class="file-field input-field">
            <div class="btn">
              <span>File</span>
              <input name="photos[]" type="file" multiple>
            </div>
            <div class="file-path-wrapper">
              <input class="file-path validate" type="text" placeholder="Upload one or more files">
            </div>
          </div>

          <br>
          描述
          <textarea name="description" id="textarea1" class="materialize-textarea"></textarea>
          <label for="textarea1">Textarea</label>

          <div class="modal-footer">
            <input class=" modal-action modal-close waves-effect waves-green btn-flat" type="submit" value="送出">
          </div>
        </form>
      </div>
    </div>

    <script>
      $('.modal-trigger').leanModal();
    </script>
<?php
  }
?>


<?php
  if ($_POST != NULL && $_FILES != NULL){
?>
    <script>Materialize.toast('請求已送出。', 4000);</script>
<?php
  }
?>