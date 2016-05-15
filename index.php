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
          if ($USER['groupname'] === 'admin' or is_groupadmin($USER['username'], $USER['groupname']))
            display_manage();
          else
            display_user();
        ?>
      </div>
  </body>
</html>

<?php
  function display_user() {
    global $USER, $MYSQLI;
    $username = $USER['username'];
    $regular_on = is_regular_on($USER['groupname']);
    $stmt = mysqli_prepare($MYSQLI, "SELECT `TimeLimit`, `TrafficLimit`, `DefaultTimeLimit` FROM `Limits` WHERE `username` = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $TimeLimit, $TrafficLimit, $DefaultTimeLimit);
    if (!mysqli_stmt_fetch($stmt)){
      $TimeLimit = -1;
      $TrafficLimit = -1;
      $DefaultTimeLimit = -1;
    }
    mysqli_stmt_close($stmt);
?>
    <div class="row center">
      <h4>個人資料</h4>
      <a href="javascript:logout()">
        登出
      </a>
    </div>
    <table class="traffic-table centered striped">
      <thead>
        <tr>
            <th>帳號名稱</th>
            <th>流入量</th>
            <th>流出量</th>
            <th>總量</th>
            <th>使用時間</th>
            <th>流量限制</th>
            <th>時間限制</th>
            <?php
              if ($regular_on){
                echo '<th>預設時間限制</th>';
              }
            ?>
        </tr>
      </thead>

      <tbody>
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td>
            <?php
              if ($TrafficLimit === -1)
                echo '∞';
              else
                echo number_format($TrafficLimit / (1024 * 1024), 0, '.', '')." MB";
            ?>
          </td>
          <td>
            <?php
              if ($TimeLimit === -1)
                echo '∞';
              else
                echo number_format($TimeLimit / 60, 0, '.', '')." 分鐘";
            ?>
          </td>
          <?php
            if ($regular_on){
              echo '<td>';
              if ($DefaultTimeLimit === -1)
                echo '∞';
              else
                echo number_format($DefaultTimeLimit / 60, 0, '.', '')." 分鐘";
              echo '</td>';
            }
          ?>
        </tr>
      </tbody>
    </table>
    <script>
      $(function(){
        get_traffic('<?php echo $username; ?>');
        setInterval("get_traffic('<?php echo $username; ?>')", 60000);
      });
    </script>
<?php
  }
?>

<?php
  function display_manage() {
    global $USER, $MYSQLI;
    $list = array();
    $regular_on = is_regular_on($USER['groupname']);
    if ($USER['groupname'] === 'admin')
      $stmt = mysqli_prepare($MYSQLI, "SELECT `username` FROM `radcheck` WHERE `attribute` = 'Cleartext-Password'");
    else{
      $stmt = mysqli_prepare($MYSQLI, "SELECT a.`username` FROM `radcheck` a, `radusergroup` b WHERE a.`username`=b.`username` AND b.`groupname`=?");
      mysqli_stmt_bind_param($stmt, "s", $USER['groupname']);
    }
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $username);
    while (mysqli_stmt_fetch($stmt)){
      $list[$username] = array();
      $list[$username]['TimeLimit'] = -1;
      $list[$username]['TrafficLimit'] = -1;
      $list[$username]['DefaultTimeLimit'] = -1;
    }
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($MYSQLI, "SELECT * FROM `Limits`");
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id, $username, $TimeLimit, $TrafficLimit, $DefaultTimeLimit);
    while (mysqli_stmt_fetch($stmt)){
      if (array_key_exists($username, $list)){
        $list[$username]['TimeLimit'] = $TimeLimit;
        $list[$username]['TrafficLimit'] = $TrafficLimit;
        $list[$username]['DefaultTimeLimit'] = $DefaultTimeLimit;
      }
    }
    mysqli_stmt_close($stmt);
?>
    <div class="row center">
      <h4>管理平台</h4>
      <p>今日 (<?php echo date("Y/m/d"); ?>) 流量資料</p>
      <p><a href="javascript:logout()">登出</a></p>
    </div>
    <table class="traffic-table centered striped">
      <thead>
        <tr>
            <?php
              $th_width = 12.5;
              if ($regular_on)
                $th_width = 11.11;
            ?>
            <th style="width: <?php echo $th_width; ?>%;">帳號名稱</th>
            <th style="width: <?php echo $th_width; ?>%;">上傳量</th>
            <th style="width: <?php echo $th_width; ?>%;">下載量</th>
            <th style="width: <?php echo $th_width; ?>%;">總量</th>
            <th style="width: <?php echo $th_width; ?>%;">使用時間</th>
            <th style="width: <?php echo $th_width; ?>%;">流量限制</th>
            <th style="width: <?php echo $th_width; ?>%;">時間限制</th>
            <?php
              if ($regular_on){
                echo '<th style="width: '.$th_width.'%;">預設時間限制</th>';
              }
            ?>
            <th style="width: <?php echo $th_width; ?>%;">編輯</th>
        </tr>
      </thead>
      <tbody>
        <?php
          foreach ($list as $username => $limit){
        ?>
            <tr>
              <td><?php echo $username; ?></td>
              <td>0 B</td>
              <td>0 B</td>
              <td>0 B</td>
              <td>0 秒</td>
              <td style="padding: 0px;">
                <?php
                  if ($limit['TrafficLimit'] === -1)
                    echo '∞';
                  else
                    echo number_format($limit['TrafficLimit'] / (1024 * 1024), 0, '.', '')." MB";
                ?>
              </td>
              <td style="padding: 0px;">
                <?php
                  if ($limit['TimeLimit'] === -1)
                    echo '∞';
                  else
                    echo number_format($limit['TimeLimit'] / 60, 0, '.', '')." 分鐘";
                ?>
              </td>
              <?php
                if ($regular_on){
                  echo '<td style="padding: 0px;">';
                  if ($limit['DefaultTimeLimit'] === -1)
                    echo '∞';
                  else
                    echo number_format($limit['DefaultTimeLimit'] / 60, 0, '.', '')." 分鐘";
                  echo '</td>';
                }
              ?>
              <td style="padding: 0px;">
                <?php
                  if ($limit['TimeLimit'] !== -1){
                ?>
                    <div class="btn" onclick="edit_limit(this)">編輯</div>
                <?php
                  }
                ?>
              </td>
            </tr>
        <?php
          }
        ?>
      </tbody>
    </table>
    <script>
      $(function(){
        get_traffic();
        setInterval("get_traffic()", 60000);
      });
    </script>
<?php
  }
?>
