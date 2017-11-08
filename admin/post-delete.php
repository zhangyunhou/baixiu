<?php

// 一旦被载入的文件不能执行两次时 一定 用 once
require_once '../functions.php';

if (empty($_GET['id'])) {
  // 缺失必要的ID参数
  die('缺失必要的ID参数');
}

$id = $_GET['id'];

// 执行删除数据的语句
// xiu_execute('delete from posts where id = ' . $id);
xiu_execute('delete from posts where id in (' . $id . ');');

// 跳转回列表页
// referer 的作用是用来标识这个请求是从哪个页面产生的
// 如果直接在浏览器的地址栏中输入地址 则没有 referer
// 从哪来回哪去
// 图片防盗链
// 微博是个好图床

$referer = $_SERVER['HTTP_REFERER'];

header('Location: ' . $referer);
