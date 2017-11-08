<?php 
require_once '../functions.php';
if (empty($_GET['id'])) {
	die('缺失必要的ID参数');
}
$id = $_GET['id'];
xiu_execute ("delete from users where id in (".$id.")");
header('Location: /admin/users.php');
 ?>