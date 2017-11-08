<?php
/**
 * 公共函数封装
 */
require_once 'config.php';

session_start();

function xiu_get_current_user () {
  // 拿到客户端请求带来的小票
  // 找到那个对应箱子，取出标识用户是否登录的数据
  // 根据这个数据判断用户是否登录

  // 如果不存在 is_logged_in 或者值为 false
  if (empty($_SESSION['current_login_user'])) {
    // 没有登录
    header('Location: /admin/login.php');
    return;
  }
  return $_SESSION['current_login_user'];
}

/**
 * 建立一个与数据库的连接返回连接对象，注意需要自己关闭连接
 * @return [type] [description]
 */
function xiu_connect () {
  $conn = mysqli_connect(BAIXIU_DB_HOST, BAIXIU_DB_USER, BAIXIU_DB_PASS, BAIXIU_DB_NAME);
  if (!$conn) {
    // 后续代码没有执行的必要了
    die('<h1>连接错误 (' . mysqli_connect_errno() . ') ' . mysqli_connect_error() . '</h1>');
  }
  return $conn;
}

/**
 * 执行一个 SQL 语句 得到执行结果(列表)
 * @param  [type] $sql [description]
 * @return [type]      [description]
 */
function xiu_fetch_all ($sql) {
  $conn = xiu_connect();

  $query = mysqli_query($conn, $sql);

  if (!$query) {
    // 查询失败
    return false;
  }

  while ($row = mysqli_fetch_assoc($query)) {
    $data[] = $row;
  }
  // $data => 全部数据

  // 释放结果集
  mysqli_free_result($query);

  // 断开与服务端的连接
  // 数据库连接是有限的，有必要在使用完了之后手动关闭掉
  mysqli_close($conn);

  return $data;
}

/**
 * 执行一个 SQL 语句 得到执行结果(单条)
 * @param  [type] $sql [description]
 * @return [type]      [description]
 */
function xiu_fetch_one ($sql) {
  return xiu_fetch_all($sql)[0];
}

/**
 * 执行一个非查询的查询语句，执行增删改语句
 * @param  [type] $sql [description]
 * @return [type]      [description]
 */
function xiu_execute ($sql) {
  $conn = xiu_connect();

  $query = mysqli_query($conn, $sql);

  if (!$query) {
    // 查询失败
    return false;
  }

  // 获取增删改语句受影响行数
  $affected_rows = mysqli_affected_rows($conn);

  // 增删改语句没有结果集需要释放
  // // 释放结果集
  // mysqli_free_result($query);

  // 断开与服务端的连接
  // 数据库连接是有限的，有必要在使用完了之后手动关闭掉
  mysqli_close($conn);

  return $affected_rows;
}

/**
 * 输出分页链接
 * @param  integer $page    当前页码
 * @param  integer $total   总页数
 * @param  string  $format  链接模板，%d 会被替换为具体页数
 * @param  integer $visible 可见页码数量（可选参数，默认为 5）
 * @example
 *   <?php xiu_pagination(2, 10, '/list.php?page=%d', 5); ?>
 */
function xiu_pagination ($page, $total, $format, $visible = 5) {
  // 计算起始页码
  // 当前页左侧应有几个页码数，如果一共是 5 个，则左边是 2 个，右边是两个
  $left = floor($visible / 2);
  // 开始页码
  $begin = $page - $left;
  // 确保开始不能小于 1
  $begin = $begin < 1 ? 1 : $begin;
  // 结束页码
  $end = $begin + $visible - 1;
  // 确保结束不能大于最大值 $total
  $end = $end > $total ? $total : $end;
  // 如果 $end 变了，$begin 也要跟着一起变
  $begin = $end - $visible + 1;
  // 确保开始不能小于 1
  $begin = $begin < 1 ? 1 : $begin;

  // 上一页
  if ($page - 1 > 0) {
    printf('<li><a href="%s">&laquo;</a></li>', sprintf($format, $page - 1));
  }

  // 省略号
  if ($begin > 1) {
    print('<li class="disabled"><span>···</span></li>');
  }

  // 数字页码
  for ($i = $begin; $i <= $end; $i++) {
    // 经过以上的计算 $i 的类型可能是 float 类型，所以此处用 == 比较合适
    $activeClass = $i == $page ? ' class="active"' : '';
    printf('<li%s><a href="%s">%d</a></li>', $activeClass, sprintf($format, $i), $i);
  }

  // 省略号
  if ($end < $total) {
    print('<li class="disabled"><span>···</span></li>');
  }

  // 下一页
  if ($page + 1 <= $total) {
    printf('<li><a href="%s">&raquo;</a></li>', sprintf($format, $page + 1));
  }
}
