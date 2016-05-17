<?php
  global $USER;
  if ($USER['groupname'] === 'admin'){
    echo '<p><a href="/">流量管理</a> | <a href="/request.php">請求列表</a> | <a href="/manage.php">帳號管理</a> | <a href="javascript:logout()">登出</a></p>';
  }
  else if (is_groupadmin($USER['username'], $USER['groupname'])){
    echo '<p><a href="/">流量管理</a> | <a href="/request.php">請求列表</a> | <a href="javascript:logout()">登出</a></p>';
  }
  else{
    echo '<p><a href="/">流量</a> | <a href="/request.php">請求列表</a> | <a href="javascript:logout()">登出</a></p>';
  }
?>