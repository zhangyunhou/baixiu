<?php

// 载入全部公共函数
require_once '../functions.php';
// 判断是否登录
xiu_get_current_user();

// 处理筛选逻辑 =============================================
// ========================================================

// 传递了筛选参数才需要 where
// $category_id = empty($_GET['category']) ?  : $_GET['category'];
$where = '1 = 1'; // 1 = 1 就相当于一个无意义的筛选，偷奸耍滑的技巧

if (isset($_GET['category']) && $_GET['category'] !== 'all') {
  // 客户端传递了分类筛选参数
  $where .= ' and posts.category_id = ' . $_GET['category'];
}

if (isset($_GET['status']) && $_GET['status'] !== 'all') {
  // 客户端传递了一个不为 all 的状态过来
  $where .= ' and posts.status = \'' . $_GET['status'] . '\'';
}

// 处理分页参数 =============================================
// ========================================================

// 页码
$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
// 页大小（每页数量）
$size = 20;
// 查询时跳过多少条
$offset = ($page - 1) * $size;

// 查询数据库中满足条件的有多少数据
$total_count = (int)xiu_fetch_one('select
  count(1) as num
from posts
inner join users on posts.user_id = users.id
inner join categories on posts.category_id = categories.id
where ' . $where)['num'];

// 根据总条数算出总页数
$total_page = (int)ceil($total_count / $size);
// 主观认为 开始数就是当前页码 - 2 但是需要考虑是否合理的情况
$begin = $page - 2 < 1 ? 1 : $page - 2;
// 主观认为 结束数就是 开始数 + 4 ，但是需要考虑是否超出总页数
$end = $begin + 4;
if ($end > $total_page) {
  // 如果超出总页数，那么结束数就是总页数
  $end = $total_page;
  // end 发生变化 begin 同时变化 但是需要考虑是否合理的情况
  $begin = $end - 4 < 1 ? 1 : $end - 4;
}

// // page => 5
// // 分页页码
// $begin = $page - 2; // => 3 要求：大于 0
// // $end = $page + 2;  // => 7 要求：小于 总页数
// if ($begin < 1) {
//   // 开始数不合理
//   // 默认就让从1开始
//   $begin = 1;
// }
// $end = $begin + 4;



// 取数据 =============================================
// ========================================================

// 获取数据
// posts 也算是一个 开发领域的专用名词，发表物
$posts = xiu_fetch_all('select
  posts.id,
  posts.title,
  posts.created,
  posts.status,
  users.nickname as user_name,
  categories.name as category_name
from posts
inner join users on posts.user_id = users.id
inner join categories on posts.category_id = categories.id
where ' . $where . '
order by posts.created desc
limit ' . $offset . ', ' . $size . ';');

$categories = xiu_fetch_all('select * from categories');

// 过滤函数 =============================================
// ========================================================

/**
 * 将英文状态描述转换为中文
 * @param  string $status 英文状态
 * @return string         中文状态
 */
function convert_status ($status) {
  // switch ($status) {
  //   case 'drafted':
  //     return '草稿';
  //   case 'published':
  //     return '已发布';
  //   case 'trashed':
  //     return '回收站';
  //   default:
  //     return '未知';
  // }

  $dict = array(
    'drafted' => '草稿',
    'published' => '已发布',
    'trashed' => '回收站'
  );

  return isset($dict[$status]) ? $dict[$status] : '未知';
}

/**
 * 转换时间的显示
 * @param  [type] $date [description]
 * @return [type]       [description]
 */
function convert_date ($date) {
  $timestamp = strtotime($date);
  // 由于 r 在时间格式中有特殊含义，如果需要原封不动的标识 一个 r 转义一下
  return date('Y年m月d日<b\r>H:i:s', $timestamp);
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>所有文章</h1>
        <a href="post-add.html" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
        <!-- 1. 这个表单的提交请求主要是为了去服务端拿到当前选中分类的数据，拿数据占主要 -->
        <!-- 2. get 提交 参数是在 URL 中体现的，刷新没问题 -->
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
          <select name="category" class="form-control input-sm">
            <option value="all">所有分类</option>
            <?php foreach ($categories as $item): ?>
            <option value="<?php echo $item['id']; ?>"<?php echo isset($_GET['category']) && $_GET['category'] == $item['id'] ? ' selected' : '' ?>><?php echo $item['name']; ?></option>
            <?php endforeach ?>
          </select>
          <select name="status" class="form-control input-sm">
            <option value="all">所有状态</option>
            <option value="drafted"<?php echo isset($_GET['status']) && $_GET['status'] == 'drafted' ? ' selected' : '' ?>>草稿</option>
            <option value="published"<?php echo isset($_GET['status']) && $_GET['status'] == 'published' ? ' selected' : '' ?>>已发布</option>
            <option value="trashed"<?php echo isset($_GET['status']) && $_GET['status'] == 'trashed' ? ' selected' : '' ?>>回收站</option>
          </select>
          <button class="btn btn-default btn-sm" type="submit">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
          <li><a href="/admin/posts.php?page=<?php echo $page - 1; ?>">上一页</a></li>
          <?php for ($i = $begin; $i <= $end; $i++): ?>
          <li<?php echo $page === $i ? ' class="active"' : ''; ?>><a href="/admin/posts.php?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
          <?php endfor; ?>
          <li><a href="/admin/posts.php?page=<?php echo $page + 1; ?>">»</a></li>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $item): ?>
          <tr>
            <td class="text-center"><input type="checkbox"></td>
            <td><?php echo $item['title']; ?></td>
            <td><?php echo $item['user_name']; ?></td>
            <td><?php echo $item['category_name']; ?></td>
            <td class="text-center"><?php echo convert_date($item['created']); ?></td>
            <td class="text-center"><?php echo convert_status($item['status']); ?></td>
            <td class="text-center">
              <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
              <a href="javascript:;" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php $current_page = 'posts'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>
