<?php
  include "config.php";

  if ($USER == NULL){
    redirect("/login.php");
    exit();
  }

  if (isset($_GET['rid'])){
    $stmt = mysqli_prepare($MYSQLI, "SELECT * FROM `Request` WHERE `id` = ?");
    mysqli_stmt_bind_param($stmt, "i", $_GET['rid']);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id, $username, $description, $date, $read);
    if (!mysqli_stmt_fetch($stmt)){
      echo json_encode(array("status"=>"request not exists"));
      exit();
    }
    mysqli_stmt_close($stmt);

    if ($USER != NULL && ($USER['username'] === $username || $USER['groupname'] === 'admin'
        || is_ingroup($username, $USER['groupname']) && is_groupadmin($USER['username'], $USER['groupname']))){

      if (is_ingroup($username, $USER['groupname']) && is_groupadmin($USER['username'], $USER['groupname'])){
        $stmt = mysqli_prepare($MYSQLI, "UPDATE `Request` SET `read`=1 WHERE `id` = ?");
        mysqli_stmt_bind_param($stmt, "i", $_GET['rid']);
        my_mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $read = 1;
      }
?>
      <table class="traffic-table centered striped">
        <thead>
          <tr>
            <th style="width: 20%;">上傳時間</th>
            <th style="width: 15%;">帳號名稱</th>
            <th style="width: 50%;">敘述</th>
            <th style="width: 15%;">狀態</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><?php echo $date; ?></td>
            <td><?php echo $username; ?></td>
            <td style="text-align: left;"><?php echo $description; ?></td>
            <td>
              <?php
                if ($read === 1)
                  echo '<font color="green">已讀</font>';
                else
                  echo '<font color="red">未讀</font>';
              ?>
            </td>
          </tr>
        </tbody>
      </table>

<?php
      $stmt = mysqli_prepare($MYSQLI, "SELECT `username`, `description`, `date` FROM `RequestResponse` WHERE `rid` = ?");
      mysqli_stmt_bind_param($stmt, "i", $_GET['rid']);
      my_mysqli_stmt_execute($stmt);
      mysqli_stmt_bind_result($stmt, $username, $description, $date);
?>

      <table class="traffic-table centered striped" style="margin-top: 16px;">
        <thead>
          <tr>
            <th style="width: 20%;">回覆時間</th>
            <th style="width: 15%;">帳號名稱</th>
            <th style="width: 65%;">敘述</th>
          </tr>
        </thead>
        <tbody>
<?php
          while (mysqli_stmt_fetch($stmt)){
            echo '<tr>';
            echo '<td>'.$date.'</td>';
            echo '<td>'.$username.'</td>';
            echo '<td style="text-align: left;">'.$description.'</td>';
            echo '</tr>';
          }
?>
        </tbody>
      </table>

      <textarea id="response" style="margin-top: 16px;"></textarea>
      <div class="btn" onclick="response_request(<?php echo $_GET['rid'];?>)" style="float: right;">回覆</div>

<?php
      mysqli_stmt_close($stmt);

      $stmt = mysqli_prepare($MYSQLI, "SELECT `id`, `data` FROM `RequestPhoto` WHERE `rid` = ?");
      mysqli_stmt_bind_param($stmt, "i", $_GET['rid']);
      my_mysqli_stmt_execute($stmt);
      mysqli_stmt_store_result($stmt);
      mysqli_stmt_bind_result($stmt, $id, $data);
      echo '<br><br>附件：<br><br><div class="row">';
      while (mysqli_stmt_fetch($stmt)){
        echo '<a target="view_window" href="/view_photo.php?id='.$id.'"><img class="col s4" src="data:image;base64,'.$data.'"></a>';
      }
      echo '</div>';
      mysqli_stmt_close($stmt);
    }
    else{
      echo json_encode(array("status"=>"permission denied"));
    }
  }
?>
