<?php
  include "config.php";

  if ($USER != NULL){
    redirect("/");
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

      </div>
  </body>
</html>

