<?php
require '../config.php';
$post = $_POST['email'];
 $coon = mysqli_connect(BAIXIU_DB_HOST, BAIXIU_DB_USER, BAIXIU_DB_PASS, BAIXIU_DB_NAME);
  if (!$coon) {
    die('链接错误');
  }
  $query = mysqli_query($coon, "select * from users where email = '{$post}' limit 1");
  if (!$query) {
   die('查询错误');
    return;
  }
  echo $user = mysqli_fetch_assoc($query)['avatar'];
