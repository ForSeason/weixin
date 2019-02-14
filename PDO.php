<?php
  header('Content-Type:text/html;charset=utf-8');
  require('settings.php');
class PDOc{
  	public static $link=null;

  	public static function _connect(){  //链接数据库
  		try{
  		    self::$link=new PDO(DATABASE_NAME, DATABASE_ID, DATABASE_PW);
        	}catch(PDOException $e){
  		        self::throwException($e->getMessage()); 
        	}
    }

  	public static function throwException($info=''){  //抛出异常
  		echo '<br/>'.$info.'<br/>';
  	}

  	public static function PDOupdate($table,$update,$limit){  //update的函数
  		if ($table=='' or $update=='' or $limit=='') {
  			throwException('PDOupdate参数错误');
  			return;
  		} else {
  		return 'UPDATE '.$table.' SET '.$update.' WHERE '.$limit.';';
  	    }
  	}

  	public static function PDOinsertUser($username,$weixinID){  //insert的函数
  	    $sql="INSERT INTO user(username,weixinID) VALUES(?,?);";
        $stmt=self::$link->prepare($sql);
        $stmt->execute(array($username,$weixinID));
  	}

    public static function PDOdeleteUser($weixinID){
        $sql="DELETE FROM user WHERE weixinID=?;";
        $stmt=self::$link->prepare($sql);
        $stmt->execute(array($weixinID));
    }


    public static function checkWeixinIDExistence($weixinID){
      $sql="SELECT * FROM user;";
      $stmt=self::$link->query($sql);
      foreach ($stmt as $row) if ($row['weixinID'] == $weixinID) return true;
      return false;
    }

    public static function checkUsernameExistence($username){
      $sql="SELECT * FROM user;";
      $stmt=self::$link->query($sql);
      foreach ($stmt as $row) if ($row['username'] == $username) return true;
      return false;
    }
      

    public static function getUsername($weixinID){
      if (self::checkWeixinIDExistence($weixinID)) {
        $sql="SELECT * FROM user;";
        $stmt=self::$link->query($sql);
        foreach ($stmt as $row) if ($row['weixinID'] == $weixinID) return $row['username'];
      } else {
        return "Unknown(".$weixinID.")";
      }
    }

    public static function getStudentID($weixinID){
      if (self::checkWeixinIDExistence($weixinID)) {
        $sql="SELECT * FROM user;";
        $stmt=self::$link->query($sql);
        foreach ($stmt as $row) if ($row['weixinID'] == $weixinID) return $row['studentID'];
      } else {
        return "Failed";
      }
    }

    public static function getParentPhone($weixinID){
      if (self::checkWeixinIDExistence($weixinID)) {
        $sql="SELECT * FROM user;";
        $stmt=self::$link->query($sql);
        foreach ($stmt as $row) if ($row['weixinID'] == $weixinID) return $row['parentPhone'];
      } else {
        return "Failed.";
      }
    }

    public static function getPhone($weixinID){
      if (self::checkWeixinIDExistence($weixinID)) {
        $sql="SELECT * FROM user;";
        $stmt=self::$link->query($sql);
        foreach ($stmt as $row) if ($row['weixinID'] == $weixinID) return $row['phone'];
      } else {
        return "Failed.";
      }
    }

    public static function getSex($weixinID){
      if (self::checkWeixinIDExistence($weixinID)) {
        $sql="SELECT * FROM user;";
        $stmt=self::$link->query($sql);
        foreach ($stmt as $row) if ($row['weixinID'] == $weixinID) return $row['sex'];
      } else {
        return "Failed.";
      }
    }
        
    public static function getAllUsernames(){
    	$sql="SELECT username FROM user";
    	$stmt=self::$link->query($sql);
      $res='';
      foreach ($stmt as $row) $res.=$row['username'].' ';
      return $res;
    }

    public static function getweixinID($username){
      if (self::checkUsernameExistence($username)) {
        $sql="SELECT * FROM user;";
        $stmt=self::$link->query($sql);
        foreach ($stmt as $row) if ($row['username'] == $username) return $row['weixinID'];
      } else {
        return "Unknown(".$username.")";
      }
    }

    public static function superGUI($postObj){
    	$str=$postObj->Content;
    	$weixinID=$postObj->FromUserName;
      $pattern='/^查询 宿舍 ([a-zA-Z\d]{5})$/';
      if (preg_match($pattern,$str)<>0) return self::findDomitoryAll(preg_replace($pattern,'$1',$str));
      $pattern='/^查询 全信息 (.*)$/';
      if (preg_match($pattern,$str)<>0) return self::findOneAll(preg_replace($pattern,'$1',$str));
      $pattern='/^查询 生日 (.*)$/';
      if (preg_match($pattern,$str)<>0) return self::findOneBirthday(preg_replace($pattern,'$1',$str));
      $pattern='/^查询 我$/';
      if (preg_match($pattern,$str)<>0) return self::findMyselfAll($weixinID);
      $pattern='/^查询 老乡$/';
      if (preg_match($pattern,$str)<>0) return self::findMyLaoxiang($weixinID);
      $pattern='/^查询 德育分$/';
      if (preg_match($pattern,$str)<>0) return self::checkMyDP($weixinID);
      $pattern='/^查询 文体分$/';
      if (preg_match($pattern,$str)<>0) return self::checkMyWP($weixinID);
      $pattern='/^查询 德育分 (.*)$/';
      if (preg_match($pattern,$str)<>0) return self::checkOneDP(preg_replace($pattern,'$1',$str));
      $pattern='/^查询 文体分 (.*)$/';
      if (preg_match($pattern,$str)<>0) return self::checkOneWP(preg_replace($pattern,'$1',$str));
      $pattern='/^查询 事件$/';
      if (preg_match($pattern,$str)<>0) return self::checkEvents();
      $pattern='/^查询 事件 (.*)$/';
      if (preg_match($pattern,$str)<>0) return self::checkEvent(preg_replace($pattern,'$1',$str));
      return '格式错误.';
     }
      
    public static function GUI($postObj){
      $str=$postObj->Content;
    	$weixinID=$postObj->FromUserName;
      $pattern='/^查询 宿舍 ([a-zA-Z\d]{5})$/';
      if (preg_match($pattern,$str)<>0) return self::findDomitoryAll(preg_replace($pattern,'$1',$str));
      $pattern='/^查询 生日 (.*)$/';
      if (preg_match($pattern,$str)<>0) return self::findOneBirthday(preg_replace($pattern,'$1',$str));
      $pattern='/^查询 我$/';
      if (preg_match($pattern,$str)<>0) return self::findMyselfAll($weixinID);
      $pattern='/^查询 老乡$/';
      if (preg_match($pattern,$str)<>0) return self::findMyLaoxiang($username);
      $pattern='/^查询 德育分$/';
      if (preg_match($pattern,$str)<>0) return self::checkMyDP($weixinID);
      $pattern='/^查询 文体分$/';
      if (preg_match($pattern,$str)<>0) return self::checkMyWP($weixinID);
      return '格式错误.';
     }
      
    public static function checkEvents(){
      $res='德育分:'."\r\n\r\n";
      $sql='SELECT * FROM dp;';
      $stmt=self::$link->query($sql);
      $arr=array();
      foreach ($stmt as $row) {
        if (!array_key_exists($row['info'],$arr)) {
          $arr[$row['info']]=1;
        }
      }
      foreach ($arr as $info => $names) {
        $res.=$info."\r\n";
      }
      $res.="-------------------\r\n";
      $res.='文体分:'."\r\n\r\n";
      $sql='SELECT * FROM wp;';
      $stmt=self::$link->query($sql);
      $arr=array();
      foreach ($stmt as $row) {
        if (!array_key_exists($row['info'],$arr)) {
          $arr[$row['info']]=1;
        }
      }
      foreach ($arr as $info => $names) {
        $res.=$info."\r\n";
      }
      return $res;
    }
     
    public static function checkEvent($info){
      $sql="SELECT weixinID FROM dp WHERE info='{$info}';";
      $stmt=self::$link->query($sql);
      $res='';
      foreach ($stmt as $row) {
        $res.=self::getUsername($row['weixinID'])."\r\n";
      }
      $sql="SELECT * FROM wp WHERE info='{$info}';";
      $stmt=self::$link->query($sql);
      foreach ($stmt as $row) {
        $res.=self::getUsername($row['weixinID'])."\r\n";
      }
      if ($res=='') {
        return 'No result.';
      } else {
        return $res;
      }
    } 

    public static function findDomitoryAll($dmt){
      $str=strtoupper($dmt);
      $domitory=substr($str,0,2);
      $room=substr($str,2,3);
      $sql="SELECT * FROM user WHERE domitory='{$domitory}' AND room='{$room}';";
      $stmt=self::$link->query($sql);
      $res='';
      foreach ($stmt as $row) {
        if ($row['domitoryMaster']=='NO') {
          $res.=$row['username']."\r\n";
        } else {
          $res.=$row['username']."(舍长)\r\n";
        }
      }
      if ($res=='') {
        return 'No result.';
      } else {
        return $res."查询结束.";
      }
  	 }
      
        
    public static function findOneBirthday($username){
      $sql="SELECT * FROM user WHERE username='{$username}';";
      $stmt=self::$link->query($sql);
      foreach ($stmt as $row) {
        $birthYear=substr($row['ID_card'],6,4);
      	$temp=substr($row['ID_card'],10,2);
        $birthMon=preg_replace('/0(\d)/','$1',$temp);
        $temp=substr($row['ID_card'],12,2);
        $birthDay=preg_replace('/0(\d)/','$1',$temp);
        return $birthYear.'年'.$birthMon.'月'.$birthDay.'日';
      }
      return 'No result.';
    }
        
      
    public static function findOneAll($username){
      $sql="SELECT * FROM user WHERE username='{$username}';";
      $stmt=self::$link->query($sql);
      $res='';
      foreach ($stmt as $row) {
        $res.='姓名：'.$row['username']."\r\n";
        $res.='性别：'.$row['sex']."\r\n";
        $res.='民族：'.$row['ethnicity']."\r\n";
        $res.='学号：'.$row['studentID']."\r\n";
        $res.='身份证号：'.$row['ID_card']."\r\n";
        $res.='宿舍：'.$row['domitory'].$row['room']."\r\n";
        $res.='是否舍长：'.$row['domitoryMaster']."\r\n";
        $res.='电话：'.$row['phone']."\r\n";
        $res.='家长姓名：'.$row['parentName']."\r\n";
        $res.='家长电话：'.$row['parentPhone']."\r\n";
        $res.='生源地：'.$row['fromWhere']."\r\n";
        $res.='政治面貌：'.$row['politicalStatus']."\r\n";
        $res.='家庭住址：'.$row['adress']."\r\n";
        $res.='邮政编码：'.$row['postalcode']."\r\n";
      }
      if ($res=='') {
        return 'No result.';
      } else {
        return $res."查询结束.";
      }
  	 }
        
    public static function findMyselfAll($weixinID){
      $sql="SELECT * FROM user WHERE weixinID='{$weixinID}';";
      $stmt=self::$link->query($sql);
      $res='';
      foreach ($stmt as $row) {
        $res.='姓名：'.$row['username']."\r\n";
        $res.='性别：'.$row['sex']."\r\n";
        $res.='民族：'.$row['ethnicity']."\r\n";
        $res.='学号：'.$row['studentID']."\r\n";
        $res.='身份证号：'.$row['ID_card']."\r\n";
        $res.='宿舍：'.$row['domitory'].$row['room']."\r\n";
        $res.='是否舍长：'.$row['domitoryMaster']."\r\n";
        $res.='电话：'.$row['phone']."\r\n";
        $res.='家长电话：'.$row['parentPhone']."\r\n";
        $res.='家长姓名：'.$row['parentName']."\r\n";
        $res.='生源地：'.$row['fromWhere']."\r\n";
        $res.='政治面貌：'.$row['politicalStatus']."\r\n";
        $res.='家庭住址：'.$row['adress']."\r\n";
        $res.='邮政编码：'.$row['postalcode']."\r\n";
      }
      if ($res=='') {
        return 'No result.';
      } else {
        return $res."查询结束.";
      }
  	 }
        
    public static function findMyLaoxiang($weixinID){
      $sql="SELECT fromWhere FROM user WHERE weixinID='{$weixinID}';";
      $stmt=self::$link->query($sql);
      $username=self::getUsername($weixinID);
      foreach ($stmt as $row) {
        $fromWhere=$row['fromWhere'];
        break;
      }
      $res='';
      $sql="SELECT username FROM user WHERE fromWhere='{$fromWhere}';";
      $stmt=self::$link->query($sql);
      foreach ($stmt as $row) {
        $res.=($row['username']==$username)?'':$row['username']."\r\n";
      }
      if ($res=='') {
        return '你...莫得老乡...';
      } else {
        return $res."查询结束.";
      }
  	 }

    public static function checkMyDP($weixinID){
      $sql="SELECT * FROM dp WHERE weixinID='{$weixinID}';";
      $stmt=self::$link->query($sql);
      $res='';
      foreach ($stmt as $row) {
        $res.='ID:'.$row['dpID'].'/'.$row['point'].'分/'.$row['info']."\r\n";
      }
      if ($res=='') {
        return '尚无记录';
      } else {
        return $res."查询结束.";
      }
    }

    public static function checkMyWP($weixinID){
      $sql="SELECT * FROM wp WHERE weixinID='{$weixinID}';";
      $stmt=self::$link->query($sql);
      $res='';
      foreach ($stmt as $row) {
        $res.='ID:'.$row['wpID'].'/'.$row['point'].'分/'.$row['info']."\r\n";
      }
      if ($res=='') {
        return '尚无记录';
      } else {
        return $res."查询结束.";
      }
    }

    public static function checkOneDP($username){
      $weixinID=self::getWeixinID($username);
      $sql="SELECT * FROM dp WHERE weixinID='{$weixinID}';";
      $stmt=self::$link->query($sql);
      $res='';
      foreach ($stmt as $row) {
        $res.='ID:'.$row['dpID'].'/'.$row['point'].'分/'.$row['info']."\r\n";
      }
      if ($res=='') {
        return '尚无记录';
      } else {
        return $res."查询结束.";
      }
    }

    public static function checkOneWP($username){
      $weixinID=self::getWeixinID($username);
      $sql="SELECT * FROM wp WHERE weixinID='{$weixinID}';";
      $stmt=self::$link->query($sql);
      $res='';
      foreach ($stmt as $row) {
        $res.='ID:'.$row['wpID'].'/'.$row['point'].'分/'.$row['info']."\r\n";
      }
      if ($res=='') {
        return '尚无记录';
      } else {
        return $res."查询结束.";
      }
    }

    public static function addDP($username,$point,$info){
      $weixinID=self::getWeixinID($username);
      $res=0;
      $pattern='/Unknown/';
      if (preg_match($pattern,$weixinID)==0){
        $sql="INSERT INTO dp(weixinID,point,info) VALUES('{$weixinID}','{$point}','{$info}');";
        $res+=self::$link->exec($sql);
      }
      return $res;
    }
        
    public static function delDP($dpID){
      $sql="DELETE FROM dp WHERE dpID='{$dpID}';";
      $res=self::$link->exec($sql);
      return $res;
    }

    public static function addWP($username,$point,$info){
      $weixinID=self::getWeixinID($username);
      $res=0;
      $pattern='/Unknown/';
      if (preg_match($pattern,$weixinID)==0){
        $sql="INSERT INTO wp(weixinID,point,info) VALUES('{$weixinID}','{$point}','{$info}');";
        $res+=self::$link->exec($sql);
      }
      return $res;
    }
        
    public static function delWP($wpID){
      $sql="DELETE FROM wp WHERE wpID='{$wpID}';";
      $res=self::$link->exec($sql);
      return $res;
    }

    public static function refleshLog(){
      $sql="SELECT * FROM log ORDER BY id DESC LIMIT 50;";
      $stmt=self::$link->query($sql);
      $res='';
      foreach ($stmt as $row) {
        $res.=$row['time'].' '.self::getUsername($row['weixinID']).' '.$row['content']."\r\n";
      }
      $res.='查询结束.';
      file_put_contents('log/LOG.txt',$res);
      return true;
    }

    public static function record($postObj){
      $time=date("Y-m-d H:i:s",time());
      $weixinID=$postObj->FromUserName;
      $content=$postObj->Content;
      $sql="INSERT INTO log(time,weixinID,content) VALUES(?,?,?);";
      $stmt=self::$link->prepare($sql);
      $stmt->execute(array($time,$weixinID,$content));
    }
}
?>