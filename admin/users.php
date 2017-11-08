<?php

// 载入全部公共函数
require_once '../functions.php';
// 判断是否登录
xiu_get_current_user(); 
// function users_mysql () {
//   if (empty($_POST['id']) || empty($_POST['email']) || empty($_POST['slug']) || empty($_POST['nickname']) || empty($_POST['password'])) {
//     $GLOBALS['message'] = '请完整输入信息';
//     return;
//   }
//   return $arrayneme = array('$id' => $_POST['id'],'$email' => $_POST['email'],'$slug' => $_POST['slug'],'$nickname' => $_POST['nickname'],'$password' => $_POST['password']);
// }
//增加分类
function add_user(){
  if (empty($_POST['email']) || empty($_POST['slug']) || empty($_POST['nickname']) || empty($_POST['password'])) {
    $GLOBALS['message'] = '请完整输入信息';
    return;
  }
  $img = isset($_FILES['avatar']['name']) ? $_FILES['avatar']['name'] : '';
  $avatar = empty($img) ?  '' : '/static/uploads/'.$img;
  $email = $_POST['email'];
  $slug = $_POST['slug'];
  $nickname = $_POST['nickname'];
  $password = $_POST['password'];
  $sql_user = "insert into users value (null, '{$slug}', '{$email}', '{$password}', '{$nickname}', '{$avatar}',null, 'activated');";
  $mysqli_affected = xiu_execute ($sql_user);
  if ($mysqli_affected === 1) {
    $GLOBALS['success'] = '添加成功';
    unset($_POST['email']);
    unset($_POST['slug']);
    unset($_POST['nickname']);
    unset($_POST['password']);
  }
}
function edit_user(){
  if (empty($_POST['id']) || empty($_POST['email']) || empty($_POST['slug']) || empty($_POST['nickname']) || empty($_POST['password'])) {
    $GLOBALS['message'] = '请完整输入信息';
    return;
  }
  $img = isset($_FILES['avatar']['name']) ? $_FILES['avatar']['name'] : '';
  $avatar = empty($img) ?  '' : '/static/uploads/'.$img;
  $id = $_POST['id'];
  $email = $_POST['email'];
  $slug = $_POST['slug'];
  $nickname = $_POST['nickname'];
  $password = $_POST['password'];
  $sql_user = "update users set slug = '{$slug}', avatar = '{$avatar}', email = '{$email}', nickname = '{$nickname}', password = '{$password}' where id = '{$id}';";
  $mysqli_affected = xiu_execute ($sql_user);
  if ($mysqli_affected === 1) {
    $GLOBALS['success'] = '修改成功';
    unset($_POST['email']);
    unset($_POST['slug']);
    unset($_POST['nickname']);
    unset($_POST['password']);
  }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (empty($_POST['id'])) {
    add_user();
  }else{
    edit_user();
  }
}
//获取查询的结果
$users = xiu_fetch_all ('select * from users');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Users &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
  <style type="text/css">
    #preview{
      width:150px;
      height:150px;
      border:1px solid #ccc;
      background-image: url("/static/assets/img/default.png");
      background-size:150px,150px;
    }
    #previewImg{
      width: 150px;
      height: 150px;
    }
  </style>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>用户</h1>
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
      <div class="row">
        <div class="col-md-4">
          <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
            <h2>添加新用户</h2>
            <input type="hidden" id="id" name="id" value="0">
            <div class="form-group">
              <label>
              <div id="preview"></div>
              <input id="avatar" class="form-control" name="avatar" type="file" accept="image/*" multiple style="display:none"></label>
            </div>
            <div class="form-group">
              <label for="email">邮箱</label>
              <input id="email" class="form-control" name="email" type="email" placeholder="邮箱" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo isset($_POST['slug']) ? $_POST['slug'] : ''; ?>">
              <p class="help-block">https://zce.me/author/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <label for="nickname">昵称</label>
              <input id="nickname" class="form-control" name="nickname" type="text" placeholder="昵称" value="<?php echo isset($_POST['nickname']) ? $_POST['nickname'] : ''; ?>">
            </div>
            <div class="form-group">
              <label for="password">密码</label>
              <input id="password" class="form-control" name="password" type="password" placeholder="密码" value="<?php echo isset($_POST['password']) ? $_POST['password'] : ''; ?>">
            </div>
            <div class="form-group">
              <button class="btn btn-primary btn-save" type="submit">添加</button>
              <button class="btn btn-primary btn-cancel" type="button" style="display: none">取消</button>
            </div>
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a id="btn-delete" class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
               <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th class="text-center" width="80">头像</th>
                <th>邮箱</th>
                <th>别名</th>
                <th>昵称</th>
                <th>状态</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $item): ?>
                <tr>
                <td class="text-center"><input data-id = "<?php echo $item['id']; ?>" type="checkbox"></td>
                <td class="text-center"><img class="avatar" src="<?php echo empty($item['avatar']) ? '/static/uploads/default.png' : $item['avatar'] ?>"></td>
                <td><?php echo $item['email']; ?></td>
                <td><?php echo $item['slug']; ?></td>
                <td><?php echo $item['nickname']; ?></td>
                <td><?php echo $item['status'] === 'activated' ? '激活' : '未激活'; ?></td>
                <td class="text-center">
                <!-- 采用自定义属性传参 -->
                  <button class="btn btn-default btn-xs btn-edit" data-avatar = "<?php echo $item['avatar']; ?>" data-id = "<?php echo $item['id']; ?>" data-email = "<?php echo $item['email']; ?>" data-slug = "<?php echo $item['slug']; ?>" data-nickname = "<?php echo $item['nickname']; ?>" data-pass = "<?php echo $item['password']; ?>">编辑</button>
                  <a href="/admin/users-delete.php?id=<?php echo  $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'users'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
  $(function () {
    //编辑按钮点击事件，采用事件委托=========
    $('tbody').on('click', '.btn-edit', function() {
      var previewImg = document.getElementById('previewImg');
      if(previewImg){
       previewImg.remove()
      }
      var id = $(this).data('id');
      var email = $(this).data('email');
      var slug = $(this).data('slug');
      var nickname = $(this).data('nickname');
      var password = $(this).data('pass');
      var avatar = $(this).data('avatar');
      // $('#avatar').trigger('change');
      var pvImg = new Image();
      pvImg.src = avatar;
      pvImg.setAttribute('id','previewImg');
      $('#preview').append(pvImg)
      $('h2').text('修改内容');
      $('.btn-save').text('保存');
      $('.btn-cancel').fadeIn();
      $('#id').val(id);
      $('#email').val(email);
      $('#slug').val(slug);
      $('#nickname').val(nickname);
      $('#password').val(password);
    });
    //点击取消时，清空输入框================
    $('.btn-cancel').on('click', function() {
      $('h2').text('添加新用户');
      $('.btn-save').text('添加');
      $('.btn-cancel').fadeOut();
      $('#id').val(0);
      $('#email').val('');
      $('#slug').val('');
      $('#nickname').val('');
      $('#password').val('');
    });
    //批量删除==============================
    //声明一个数组保存所选的值
    var checkeds = [];
    //改变事件，采用事件委托
    var btnDelete = $('#btn-delete');
    $('tbody').on('change', 'input', function() {
      var $this = $(this);
      var id = $this.data('id');
      //判断发生改变时，如果该选框已被选择，则将该行id加入数组中，否则，查找该id在数组的位置，并移除
      if ($this.prop('checked')) {
        checkeds.push(id);
      }else{
        checkeds.splice(checkeds.indexOf(id), 1);
      };
      // console.log(checkeds);
      // 数组长度为零时不显示，不为零时显示
      checkeds.length ? btnDelete.fadeIn() : btnDelete.fadeOut();
      //将数组中的数据传入批量删除的a标签的href属性内
      btnDelete.attr({'href': '/admin/users-delete.php?id='+checkeds});
    });
    //全选、全部选
    $('thead input').on('change', function(event) {
      checkeds = [];
      $('tbody input').prop('checked', $(this).prop('checked')).trigger('change');
    });

    // 头像预览
  //URL.createObjectURL 方式
    document.getElementById('avatar').onchange = function(e){
      var previewImg = document.getElementById('previewImg');
      if(previewImg){
       previewImg.remove()
      }
        var ele =  document.getElementById('avatar').files[0];

        var createObjectURL = function(blob){
          return window[window.webkitURL ? 'webkitURL' : 'URL']['createObjectURL'](blob);
        };
        var newimgdata = createObjectURL(ele);
        // console.log(newimgdata);
        var pvImg = new Image();
        pvImg.src = newimgdata;
        pvImg.setAttribute('id','previewImg');
        // console.dir(pvImg);
        $('#preview').append(pvImg);
    }
  })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
