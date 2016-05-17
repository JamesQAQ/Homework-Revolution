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
      $stmt = mysqli_prepare($MYSQLI, "SELECT `data` FROM `RequestPhoto` WHERE `rid` = ?");
      mysqli_stmt_bind_param($stmt, "i", $_GET['rid']);
      my_mysqli_stmt_execute($stmt);
      mysqli_stmt_store_result($stmt);
      mysqli_stmt_bind_result($stmt, $data);
      echo '<br><br>附件：<br><br><div class="row">';
      while (mysqli_stmt_fetch($stmt)){
        echo '<img class="col s4" src="data:image;base64,'.$data.'">';
      }
      echo '</div>';
      mysqli_stmt_close($stmt);
    }
    else{
      echo json_encode(array("status"=>"permission denied"));
    }
  }
?>
