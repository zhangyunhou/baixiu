<?php

session_start();

// 找到当前访问用户的箱子
// 删除这个箱子里面用来标识用户登录状态的数据
unset($_SESSION['current_login_user']);

// 跳转回登录页
header('Location: /admin/login.php');

// 如果页面中没有给出具体的错误信息，可以尝试去错误日志中找到
