<?php
  include "config.php";
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
          if ($USER != NULL){
            if ($USER['groupname'] === 'admin')
              display_manage();
            else
              display_user();
          }
          else
            display_login();
        ?>
      </div>
  </body>
</html>

<?php
  function display_login() {
?>
    <div class="row center">
      <h4>登入帳號使用 HR 管理平台</h4>
    </div>
    <div class="row">
      <form class="col l6 offset-l3 m8 offset-m2 s12 z-depth-2 login-container" style="padding: 25px;">
        <div class="row">
          <div class="input-field col s12">
            <input id="username" name="username" type="text" class="validate">
            <label for="username">帳號</label>
          </div>
        </div>
        <div class="row">
          <div class="input-field col s12">
            <input id="password" name="password" type="password" class="validate">
            <label for="password">密碼</label>
          </div>
        </div>
        <div class="row">
          <a id="login" class="col l3 offset-l2 s4 offset-s1 waves-effect waves-light btn">登入</a>
          <a class="col l3 offset-l2 s4 offset-s2 waves-effect waves-light btn disabled">忘記密碼</a>
        </div>
      </form>
      <script>
        $(function(){
          $("#login").click(function(e){
            e.preventDefault();
            login($("#username").val(), $("#password").val());
          });
        });
      </script>
    </div>
<?php
  }
?>

<?php
  function display_user() {
    global $USER, $MYSQLI;
    $username = $USER['username'];
    $stmt = mysqli_prepare($MYSQLI, "SELECT `TimeLimit`, `TrafficLimit` FROM `Limits` WHERE `username` = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $TimeLimit, $TrafficLimit);
    if (!mysqli_stmt_fetch($stmt)){
      $TimeLimit = -1;
      $TrafficLimit = -1;
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
    global $MYSQLI;
    $list = array();
    $stmt = mysqli_prepare($MYSQLI, "SELECT `username` FROM `radcheck` WHERE `attribute` = 'Cleartext-Password'");
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $username);
    while (mysqli_stmt_fetch($stmt)){
      $list[$username] = array();
      $list[$username]['TimeLimit'] = -1;
      $list[$username]['TrafficLimit'] = -1;
    }
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($MYSQLI, "SELECT * FROM `Limits`");
    my_mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id, $username, $TimeLimit, $TrafficLimit);
    while (mysqli_stmt_fetch($stmt)){
      $list[$username]['TimeLimit'] = $TimeLimit;
      $list[$username]['TrafficLimit'] = $TrafficLimit;
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
            <th style="width: 12.5%;">帳號名稱</th>
            <th style="width: 12.5%;">上傳量</th>
            <th style="width: 12.5%;">下載量</th>
            <th style="width: 12.5%;">總量</th>
            <th style="width: 12.5%;">使用時間</th>
            <th style="width: 12.5%;">流量限制</th>
            <th style="width: 12.5%;">時間限制</th>
            <th style="width: 12.5%;">編輯</th>
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
