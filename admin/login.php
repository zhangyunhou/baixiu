<?php
//检测是否已经登录
session_start();
if (isset($_SESSION['current_login_user'])) {
  header('Location: /admin/');
  return;
}
function login () {
  // 1. 接收表单数据
  if (empty($_POST['email'])) {
    // 没有填写用户名
    $GLOBALS['message'] = '有没有用户名，没有搞什么飞机？';
    return;
  }

  if (empty($_POST['password'])) {
    // 没有填写密码
    $GLOBALS['message'] = '忘记了吗？我也不知道';
    return;
  }

  $email = $_POST['email'];
  $password = $_POST['password'];

  // 2. 校验数据（业务）
  // 载入配置
  require '../functions.php';
  // $conn = mysqli_connect(BAIXIU_DB_HOST, BAIXIU_DB_USER, BAIXIU_DB_PASS, BAIXIU_DB_NAME);
  // if (!$conn) {
  //   die('连接数据库失败');
  // }
  $conn = xiu_connect ();
  $query = mysqli_query($conn, "select * from users where email = '{$email}' limit 1;");
  if (!$query) {
    // 查询数据失败
    $GLOBALS['message'] = '登录失败，再试一次呗';
    return;
  }

  $user = mysqli_fetch_assoc($query);
  if (!$user) {
    // 用户名不存在
    $GLOBALS['message'] = '用户名或密码不正确';
    return;
  }

  // 一般密码是加密存储的，常见的加密手段：md5
  if ($user['password'] !== $password) {
    $GLOBALS['message'] = '用户名或密码不正确';
    return;
  }

  // 用户名密码都正确 登录成功
  // 在服务端找一个空箱子 记下箱子的编号 往箱子里面放一个数据（用来标识用户是否登录了）
  
  // 将编号以小票的形式发送给客户端
  // 一般直接在 Session 中放当前登录用户信息
  $_SESSION['current_login_user'] = $user;

  // 3. 响应
  header('Location: /admin/');
}

// 判断是否是表单提交的请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  login();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
</head>
<body>
  <div class="login">
    <!-- novalidate 是 h5 新的表单特性 作用是 关闭表单的校验功能 -->
    <form class="login-wrap" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" novalidate>
      <img class="avatar" src="/static/assets/img/default.png">
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)): ?>
      <div class="alert alert-danger">
        <strong>错误！</strong> <?php echo $message; ?>
      </div>
      <?php endif ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" name="email" type="email" class="form-control" placeholder="邮箱" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" autofocus>
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block" type="submit">登 录</button>
    </form>
  </div>
  <!-- 有时间添加一个客户端校验功能 -->
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
  <script>
    $(function ($) {
      // 某一个时机
      // 某一件事情

      // 匹配邮箱的格式，实际开发用成型的
      var reg = /^[a-zA-Z0-9-_]+@[a-zA-Z0-9-_.]+$/
      var $avatar = $('.avatar')

      $('#email').on('blur', function () {
        var email = $(this).val()
        // 如果没有输入邮箱内容不再继续
        if (!email) return
        if (!reg.test(email)) return

        // 获取当前邮箱对应的头像地址
        // 由于头像和邮箱之间的对应关系在服务端存放
        // 这里是在客户端执行的代码
        // 必然涉及 AJAX

        $.get('/admin/api/avatar.php', { email: email }, function (src) {
          // src => '/sdfsd/foo.png'
          $avatar.fadeOut(function () {
            $avatar.attr('src', src).fadeIn()
          })
        })
      })
    })
  </script>
</body>
</html>
