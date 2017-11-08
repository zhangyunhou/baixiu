<?php

// 如果没有提交邮箱，那就返回空
if (empty($_GET['email'])) {
  exit();
}

// 接收 email
$email = $_GET['email'];

// 返回头像
require '../../config.php';
$conn = mysqli_connect(BAIXIU_DB_HOST, BAIXIU_DB_USER, BAIXIU_DB_PASS, BAIXIU_DB_NAME);
if (!$conn) {
  die('连接数据库失败');
}

$query = mysqli_query($conn, "select * from users where email = '{$email}' limit 1;");
if (!$query) {
  // 查询数据失败
  exit();
}

$user = mysqli_fetch_assoc($query);
if (!$user) {
  // 用户名不存在
  exit();
}

echo $user['avatar'];
