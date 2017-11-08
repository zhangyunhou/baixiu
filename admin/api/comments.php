<?php

// 负责返回评论数据的数据接口
// 1. 查询数据库中的评论数据
require_once '../../functions.php';

// 处理分页参数 ========================
// ===================================
$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
$size = 20;
// 越过多少条
$skip = ($page - 1) * $size;

// 取数据 =============================
// ===================================

$comments = xiu_fetch_all("select
  comments.*,
  posts.title as post_title
from comments
inner join posts on comments.post_id = posts.id
order by comments.created desc
limit {$skip}, {$size}");

// 查询总条数
$total_count = (int)xiu_fetch_one('select
  count(1) as i
from comments
inner join posts on comments.post_id = posts.id')['i'];

// 计算总页数
$total_pages = ceil($total_count / $size);

// 2. 序列化为JSON
// 不能只是将数据返回，还需要将总页数返回
// $json_str = json_encode($comments);

$json_str = json_encode(array(
  'comments' => $comments,
  'total_pages' => $total_pages
));


// 3. 响应给客户端
header('Content-Type: application/json');
echo $json_str;



// 没有对数据进行分页 =======================================
//
// // 负责返回评论数据的数据接口
// // 1. 查询数据库中的评论数据
// require_once '../../functions.php';

// $comments = xiu_fetch_all('select
//   comments.*,
//   posts.title as post_title
// from comments
// inner join posts on comments.post_id = posts.id;');

// // 2. 序列化为JSON
// $json_str = json_encode($comments);

// // 3. 响应给客户端
// header('Content-Type: application/json');
// echo $json_str;
