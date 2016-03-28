<?php
  include "config.php";
  $uamsecret = UAM_SECRET;
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Homework Revolution wifi</title>
    <link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="css/main.css">
    <script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="js/materialize.min.js"></script>
    <script type="text/javascript" src="js/main.js"></script>
    <style type="text/css">
    <!--
      form.login-container{
        background-color: rgba(255, 255, 255, 0.8);
        -moz-border-radius: 10px;
        -webkit-border-radius: 10px;
        border-radius: 10px;
      }
    -->
    </style>
  </head>
  <body>
    <?php
      $login_mandatory_params = array('chal', 'uamip', 'uamport', 'username', 'password');
      $login_params = array_intersect(array_keys($_GET), $login_mandatory_params);
    ?>
      <div class="container">
        <div class="row center">
          <h2 class="blue-text">Homework Revolution</h2>
        </div>
      <?php
        if (!array_diff($login_mandatory_params, $login_params))
          attempt_login();

        if (isset($_GET['res'])){
          switch ($_GET['res']) {
            case 'notyet':
              display_notyet();
              break;
            case 'failed':
              display_failed();
              break;
            case 'success':
              display_success();
              break;
            case 'logoff':
            case 'timeout':
              display_logoff();
              break;
            case 'already':
              display_already();
              break;
          }
        }
      ?>
      </div>
  </body>
</html>

<?php
  function display_notyet() {
?>
    <div class="row center">
      <h4>登入帳號使用 HR-wifi</h4>
    </div>
    <div class="row">
      <form method="get" action="" class="col l6 offset-l3 m8 offset-m2 s12 z-depth-2 login-container" style="padding: 25px;">
        <input type="hidden" name="chal" value="<?php echo $_GET['challenge']; ?>">
        <input type="hidden" name="uamip" value="<?php echo $_GET['uamip']; ?>">
        <input type="hidden" name="uamport" value="<?php echo $_GET['uamport']; ?>">
        <input type="hidden" name="userurl" value="<?php echo $_GET['userurl']; ?>">
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
        <script>
          function login(){
            window.location = "?chal=" + $("[name='chal']").val() + "&uamip=" + $("[name='uamip']").val()
                            + "&uamport=" + $("[name='uamport']").val() + "&userurl=" + $("[name='userurl']").val()
                            + "&username=" + $("[name='username']").val() + "&password=" + $("[name='password']").val();
          }
        </script>
        <div class="row">
          <a href="javascript:login()" class="col l3 offset-l2 s4 offset-s1 waves-effect waves-light btn">登入</a>
          <a href="javascript:$('#modal-add').openModal()" class="col l3 offset-l2 s4 offset-s2  waves-effect waves-light btn">註冊帳號</a>
        </div>
      </form>
    </div>

    <div id="modal-add" class="modal">
      <div class="modal-content">
        <h4>註冊帳號</h4>
        <form class="col s12" style="padding: 25px;">
          <div class="row">
            <div class="input-field col s12">
              <input id="reg-username" type="text" class="validate">
              <label for="reg-username">帳號</label>
            </div>
            <div class="input-field col s12">
              <input id="reg-password" type="password" class="validate">
              <label for="reg-password">密碼</label>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <a href="javascript:register()" class="orange lighten-1 white-text btn-flat">註冊</a>
      </div>
    </div>
<?php
  }
?>

<?php
  function attempt_login() {
    global $uamsecret;

    $hexchal = pack("H32", $_GET['chal']);
    $newchal = $uamsecret ? pack("H*", md5($hexchal.$uamsecret)) : $hexchal;

    $response = md5("\0".$_GET['password'].$newchal);
    
    # $newpwd = pack("a32", $_GET['password']);
    # $pappassword = implode('', unpack("H32", ($newpwd ^ $newchal)));

    $logon_url = 'http://'.$_GET['uamip'].':'.$_GET['uamport'].'/'.'logon?username='.$_GET['username'].'&response='.$response.'&userurl='.$_GET['userurl'];
?>
    <div class="row center">
      <h4>登入中</h4>
    </div>
    <br><br><br><br>
    <div class="progress">
      <div class="indeterminate"></div>
    </div>
    <script>
      window.location = '<?php echo $logon_url; ?>';
    </script>
<?php
  }
?>

<?php
  function display_failed() {
    $prelogin_url = 'http://'.$_GET['uamip'].':'.$_GET['uamport'].'/prelogin?userurl='.$_GET['userurl'];
    $host = $_GET['uamip'].':'.$_GET['uamport'];
?>
    <div class="row center">
      <h4>登入失敗</h4>
      <a href="<?php echo $prelogin_url; ?>">
        請在嘗試一次
      </a>
    </div>    
<?php
  }
?>

<?php
  function display_success() {
    $logoff_url = 'http://'.$_GET['uamip'].':'.$_GET['uamport'].'/logoff';
    $username = $_GET['uid'];
?>
    <div class="row center">
      <h4>登入成功</h4>
      <p>今日 (<?php echo date("Y/m/d"); ?>) 流量資料</p>
      <p><a href="<?php echo $logoff_url; ?>">登出</a></p>
    </div>
    <table class="traffic-table centered striped">
      <thead>
        <tr>
            <th>帳號名稱</th>
            <th>上傳量</th>
            <th>下載量</th>
            <th>總量</th>
            <th>使用時間</th>
        </tr>
      </thead>

      <tbody>
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
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
  function display_logoff() {
    $prelogin_url = 'http://'.$_GET['uamip'].':'.$_GET['uamport'].'/prelogin?userurl='.$_GET['userurl'];
?>
    <div class="row center">
      <h4>登出成功</h4>
      <a href="<?php echo $prelogin_url; ?>">
        使用其他帳號登入
      </a>
    </div>  
<?php
  }
?>

<?php
  function display_already() {
    $logoff_url = 'http://'.$_GET['uamip'].':'.$_GET['uamport'].'/logoff';
?>
    <div class="row center">
      <h4>已經是登入狀態</h4>
      <a href="<?php echo $logoff_url; ?>">
        登出以使用其他帳號登入
      </a>
    </div>
<?php
  }
?>