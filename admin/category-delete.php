<?php

// 一旦被载入的文件不能执行两次时 一定 用 once
require_once '../functions.php';

if (empty($_GET['id'])) {
  // 缺失必要的ID参数
  die('缺失必要的ID参数');
}

$id = $_GET['id'];

// 执行删除数据的语句
// xiu_execute('delete from categories where id = ' . $id);
xiu_execute('delete from categories where id in (' . $id . ');');

// 跳转回列表页
header('Location: /admin/categories.php');
