<?php
header('Content-Type:text/html;charset=utf-8');
date_default_timezone_set("Asia/Shanghai");
require('PDO.php');
PDOc::_connect();
$sql="SELECT * FROM log ORDER BY id DESC;";
$res='';
$stmt=PDOc::$link->query($sql);
foreach ($stmt as $row) {
  $res.=$row['time'].' '.PDOc::getUsername($row['weixinID']).' '.$row['content'].'<br/>';
}
$res.='查询结束.';
echo $res;
?>

