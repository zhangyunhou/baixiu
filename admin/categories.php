<?php

// 载入全部公共函数
require_once '../functions.php';
// 判断是否登录
xiu_get_current_user();
// function mysql_category () {
//   if (empty($_POST['id']) || empty($_POST['name']) || empty($_POST['slug'])) {
//     // 表单为完整填写
//     $GLOBALS['message'] = '请完整填写表单';
//     return;
//   }

//   $id = $_POST['id'];
//   $name = $_POST['name'];
//   $slug = $_POST['slug'];
// }
// 处理新增分类的逻辑
function add_category () {
  // 1. 获取客户端提交的数据
  // 2. 校验
  // 3. 持久化
  // 4. 响应
  if (empty($_POST['name']) || empty($_POST['slug'])) {
    // 表单为完整填写
    $GLOBALS['message'] = '请完整填写表单';
    return;
  }

  $name = $_POST['name'];
  $slug = $_POST['slug'];


  // 是边界还是内容
  // "insert into categories values (null, 'foo', '好的');"
  // $sql = 'insert into categories values (null, \'' . $slug . '\', \'' . $name . '\');'
  // $sql = "insert into categories values (null, '" . $slug . "', '" . $name . "');"
  // $sql = "insert into categories values (null, '" . $slug . "', '" . $name . "');"

  $sql = "insert into categories values (null, '{$slug}', '{$name}');";

  $affected_rows = xiu_execute($sql);

  if ($affected_rows === 1) {
    $GLOBALS['success'] = '添加成功';
    unset($_POST['name']);
    unset($_POST['slug']);
  }
}

function edit_category () {
 if (empty($_POST['id']) || empty($_POST['name']) || empty($_POST['slug'])) {
    // 表单为完整填写
    $GLOBALS['message'] = '请完整填写表单';
    return;
  }

  $id = $_POST['id'];
  $name = $_POST['name'];
  $slug = $_POST['slug'];
  $sql = "update categories set slug = '{$slug}', name = '{$name}' where id = {$id}";

  $affected_rows = xiu_execute($sql);

  if ($affected_rows === 1) {
    $GLOBALS['success'] = '修改成功';
    unset($_POST['name']);
    unset($_POST['slug']);
  }
}

// 肯定是先处理增删改，最后查询
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // 到底是添加还是修改
  if (empty($_POST['id'])) {
    add_category();
  } else {
    edit_category();
  }
}

// 获取数据库中全部分类的数据
$categories = xiu_fetch_all('select * from categories;');

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
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
        <h1>分类目录</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <?php if (isset($message)): ?>
      <div class="alert alert-danger">
        <strong>错误！</strong> <?php echo $message; ?>
      </div>
      <?php endif ?>

      <?php if (isset($success)): ?>
      <div class="alert alert-success">
        <strong>成功！</strong> <?php echo $success; ?>
      </div>
      <?php endif ?>
      <div class="row">
        <div class="col-md-4">
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <h2>添加新分类目录</h2>
            <!-- 隐藏域的特点是看不见，但是也可以像其他表单域一样提交数据 -->
            <input type="hidden" id="id" name="id" value="0">
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称" value="<?php echo isset($_POST['name']) ? $_POST['name'] : '' ?>">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo isset($_POST['slug']) ? $_POST['slug'] : '' ?>">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary btn-save" type="submit">添加</button>
              <button class="btn btn-default btn-cancel" type="button" style="display: none;">取消</button>
            </div>
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a id="btn_delete" class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($categories as $item): ?>
              <tr>
                <td class="text-center"><input data-id="<?php echo $item['id']; ?>" type="checkbox"></td>
                <td><?php echo $item['name']; ?></td>
                <td><?php echo $item['slug']; ?></td>
                <td class="text-center">
                  <button class="btn btn-info btn-xs btn-edit" data-id="<?php echo $item['id']; ?>" data-name="<?php echo $item['name']; ?>" data-slug="<?php echo $item['slug']; ?>">编辑</button>
                  <a href="/admin/category-delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'categories'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>

    // var $tbodyCheckboxs = $('tbody input')
    // var $btnDelete = $('#btn_delete')

    // $tbodyCheckboxs.on('change', function () {
    //   // 只要有任意一个复选框选择状态变化都会执行这里
    //   var show = false
    //   $tbodyCheckboxs.each(function (i, item) {
    //     if ($(item).prop('checked')) {
    //       show = true
    //     }
    //   })

    //   show ? $btnDelete.fadeIn() : $btnDelete.fadeOut()
    // })


    // version 2 =====================================

    // $(function ($) {
    //   var $tbodyCheckboxs = $('tbody input')
    //   var $btnDelete = $('#btn_delete')

    //   // 这个数组始终存放选中行对应数据ID
    //   var checkeds = []
    //   $tbodyCheckboxs.on('change', function () {
    //     // 只要有任意一个复选框选择状态变化都会执行这里
    //     var $this = $(this)
    //     var id = $this.attr('data-id')

    //     if ($this.prop('checked')) {
    //       checkeds.push(id)
    //     } else {
    //       checkeds.splice(checkeds.indexOf(id), 1)
    //     }

    //     // 根据有没有选中显示或隐藏
    //     checkeds.length ? $btnDelete.fadeIn() : $btnDelete.fadeOut()
    //     // 改变批量删除链接的问号参数
    //     $btnDelete.attr('href', '/admin/category-delete.php?id=' + checkeds)
    //   })
    // })
    //

    // version 3 =============================================
    $(function ($) {
      var $btnDelete = $('#btn_delete')

      // 这个数组始终存放选中行对应数据ID
      var checkeds = []
      // 委托事件的方式效率高
      $('tbody').on('change', 'input', function () {
        // 只要有任意一个复选框选择状态变化都会执行这里
        var $this = $(this)
        // data-xxx 是 H5 的标准，jquery有专门的获取这一类属性的方法
        // 原生用 dataset
        var id = $this.data('id')

        if ($this.prop('checked')) {
          checkeds.push(id)
        } else {
          checkeds.splice(checkeds.indexOf(id), 1)
        }

        // 根据有没有选中显示或隐藏
        checkeds.length ? $btnDelete.fadeIn() : $btnDelete.fadeOut()
        // 改变批量删除链接的问号参数
        $btnDelete.attr('href', '/admin/category-delete.php?id=' + checkeds)
      })

      // 全选 、 全不选
      var $tbodyCheckboxs = $('tbody input')
      $('thead input').on('change', function () {
        var checked = $(this).prop('checked')
        $tbodyCheckboxs
          .prop('checked', checked)
          .trigger('change')
      })


      // 编辑功能的 JS
      $('tbody').on('click', '.btn-edit', function () {
        // 将当前行中的数据信息 展示到左边的表单上
        var id = $(this).data('id')
        var name = $(this).data('name')
        var slug = $(this).data('slug')

        $('form h2').text('编辑分类')
        $('form .btn-save').text('保存')
        $('form .btn-cancel').fadeIn()
        $('#id').val(id) // 设置隐藏域中的ID
        $('#name').val(name)
        $('#slug').val(slug)
      })

      // 取消编辑
      $('.btn-cancel').on('click', function () {
        $('form h2').text('添加新分类目录')
        $('form .btn-save').text('添加')
        $('form .btn-cancel').fadeOut()
        $('#id').val(0)
        $('#name').val('')
        $('#slug').val('')
        return false // 组织当前按钮导致的表单提交
      })
    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
