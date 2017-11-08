<?php

// 载入全部公共函数
require_once '../functions.php';
// 判断是否登录
$user = xiu_get_current_user();
$categories = xiu_fetch_all('select * from categories');
function add_posts () {
  if (empty($_POST['title']) || empty($_POST['content']) || empty($_POST['slug']) || empty($_POST['category']) || empty($_POST['created']) || empty($_POST['status']) || empty($_FILES['feature']['name'])) {
    $GLOBALS['message'] = '请完整输入信息';
    return;
  }
  global $user;
  $id = $user['id'];
  $img = isset($_FILES['feature']['name']) ? $_FILES['feature']['name'] : '';
  $avatar = empty($img) ?  '' : '/static/uploads/'.$img;
  $title = $_POST['title'];
  $content = $_POST['content'];
  $slug = $_POST['slug'];
  $category = $_POST['category'];
  $created = $_POST['created'];
  $status = $_POST['status'];
  $sql_post = "insert into posts value(null, '{$slug}', '{$title}', '{$avatar}', '{$created}', '{$content}', 120, 120, '{$status}', '{$id}', '{$category}');";
  $mysqli_affected = xiu_execute ($sql_post);
  // if ($mysqli_affected === 1) {
  //   $GLOBALS['success'] = '修改成功';
  //   unset($_POST['title']);
  //   unset($_POST['content']);
  //   unset($_POST['slug']);
  //   unset($_POST['category']);
  //   unset($_POST['created']);
  //   unset($_POST['status']);
  //   unset($_FILES['feature']);
  // }
   header('Location: /admin/posts.php');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    add_posts();
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Add new post &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/vendors/simplemde/simplemde.min.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>写文章</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)): ?>
        <div class="alert alert-danger">
        <strong>错误！</strong><?php echo $message ?>
      </div>
      <?php endif ?>
      <?php if (isset($success)): ?>
        <div class="alert alert-success">
        <strong>正确！</strong><?php echo $success ?>
      </div>
      <?php endif ?>
      <form class="row" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
        <div class="col-md-9">
          <div class="form-group">
            <label for="title">标题</label>
            <input id="title" class="form-control input-lg" name="title" type="text" placeholder="文章标题">
          </div>
          <div class="form-group">
            <label for="content">内容</label>
            <textarea id="content" class="form-control input-lg" name="content" cols="30" rows="10" placeholder="内容"></textarea>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="slug">别名</label>
            <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
            <p class="help-block">https://zce.me/post/<strong>slug</strong></p>
          </div>
          <div class="form-group">
            <label for="feature">特色图像</label>
            <!-- show when image chose -->
            <img id="preview" class="help-block thumbnail" style="display: none">
            <input id="feature" class="form-control" name="feature" type="file">
          </div>
          <div class="form-group">
            <label for="category">所属分类</label>
            <select id="category" class="form-control" name="category">
            <?php foreach ($categories as $item): ?>
            <option value="<?php echo $item['id']; ?>"<?php echo isset($_GET['category']) && $_GET['category'] == $item['id'] ? ' selected' : '' ?>><?php echo $item['name']; ?></option>
            <?php endforeach ?>
            </select>
          </div>
          <div class="form-group">
            <label for="created">发布时间</label>
            <input id="created" class="form-control" name="created" type="datetime-local">
          </div>
          <div class="form-group">
            <label for="status">状态</label>
            <select id="status" class="form-control" name="status">
              <option value="drafted">草稿</option>
              <option value="published">已发布</option>
              <option value="trashed">垃圾箱</option>
            </select>
          </div>
          <div class="form-group">
            <button class="btn btn-primary" type="submit">保存</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php $current_page = 'post-add'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/simplemde/simplemde.min.js"></script>
  <script>NProgress.done()</script>
  <script>
    $(function ($) {
      new SimpleMDE({
        element: $('#content')[0],
        spellChecker: false
      })

      // 找一个合适的时机
      // 做一件合适的事情
      $('#feature').on('change', function () {
        // 这里的代码会在用户选择文件过后执行
        if (!this.files.length) return

        // 肯定选择了一个文件

        var file = this.files[0]

        // 判断是是图片文件
        if (!file.type.startsWith('image/')) return

        // 肯定是一个图片文件
        // 为这个文件分配一个临时的地址
        var url = URL.createObjectURL(file)

        // 处理事件重复注册问题
        $('#preview').attr('src', url).fadeIn().on('load', function () {
          // 吊销这个地址 一定是在图片的 onload 事件中执行
          URL.revokeObjectURL(url)
        })
      })
    })
  </script>
</body>
</html>
