<HTML>
<HEAD><TITLE>Log</TITLE></HEAD>
<BODY>
<?php
header('Content-Type:text/html;charset=utf-8');
date_default_timezone_set("Asia/Shanghai");
require('PDO.php');
PDOc::_connect();
$username=isset($_POST['username'])?$_POST['username']:'';
$password=isset($_POST['password'])?$_POST['password']:'';
if (strtolower($username)=='username' and strtolower($password)=='password') {
    $sql="SELECT * FROM log ORDER BY id DESC;";
    $res='';
    $stmt=PDOc::$link->query($sql);
    foreach ($stmt as $row) {
        $res.=$row['time'].' '.PDOc::getUsername($row['weixinID']).' '.$row['content'].'<br/>';
    }
    $res.='查询结束.';
    echo $res;
} else {
    echo '验证失败！';
}
?>
<br/>
<a href="log.php"><input type="button" value="返回"></a>
</BODY>
</HTML>