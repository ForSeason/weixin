<?php
require('PDO.php');
require('simple_html_dom.php');
require('Tuling123.php');
require('settings.php');
PDOc::_connect();
  class weixin {
      
  
    public static $textTemplate="<xml> 
            <ToUserName><![CDATA[%s]]></ToUserName> 
            <FromUserName><![CDATA[%s]]></FromUserName> 
            <CreateTime>%s</CreateTime> 
            <MsgType><![CDATA[%s]]></MsgType> 
            <Content><![CDATA[%s]]></Content> 
            </xml>";

    public static $responded=false;
  
    public static function responseSubscribe($postObj){
      if (strtolower($postObj->Event=='subscribe')){
          $toUser=$postObj->FromUserName;
          $fromUser=$postObj->ToUserName;
          $time=time();
          $MsgType='text';
          $Content='欢迎！输入“关键字”可以查看本公众号的功能！';
          $template=self::$textTemplate;
          $info=sprintf($template,$toUser,$fromUser,$time,$MsgType,$Content);
          echo $info;
          //file_put_contents('log.txt',$info);
        }
    }



    public static function responseKeyWords($postObj){
      $keyword='关键字';
      $Content=self::readFile('contents/keywords.txt');
      self::responseText($postObj,$keyword,$Content);
      $keyword='关键词';
      $Content=self::readFile('contents/keywords.txt');
      self::responseText($postObj,$keyword,$Content);
      $keyword='教材';
      $Content=self::readFile('contents/textbooks.txt');
      self::responseText($postObj,$keyword,$Content);
      $keyword='课程表';
      $Content="下一节课是:\n".self::nextClass()."\n\n".self::timeTable();
      self::responseText($postObj,$keyword,$Content);
      $keyword='课表';
      $Content="下一节课是:\n".self::nextClass()."\n\n".self::timeTable();
      self::responseText($postObj,$keyword,$Content);
      $keyword='软件';
      $Content=self::readFile('contents/software.txt');
      self::responseText($postObj,$keyword,$Content);
      $keyword='作业';
      $Content=self::readFile('contents/homework.txt');
      self::responseText($postObj,$keyword,$Content);
      $keyword='作业栏';
      $Content=self::readFile('contents/homework.txt');
      self::responseText($postObj,$keyword,$Content);
      $keyword='下一节课';
      $Content=self::nextClass();
      self::responseText($postObj,$keyword,$Content);
      $keyword='下节课';
      $Content=self::nextClass();
      self::responseText($postObj,$keyword,$Content);
      $keyword='新闻';
      $Content=self::getNews();
      self::responseText($postObj,$keyword,$Content);
      self::responseGetUserInfo($postObj);
      self::responseWeather($postObj);
      self::responseACWeather($postObj);
      self::responseTranslate($postObj);
      self::responseBaike($postObj);
      self::responseFeedback($postObj);
      self::responseAddDP($postObj);
      self::responseAddWP($postObj);
      self::responseDelDP($postObj);
      self::responseDelWP($postObj);
      //self::responseRegister($postObj);
      //self::responseClose($postObj);
      $keyword='主赛日报名';
      $filename='log/zhusairiSignUp.txt';
      self::responseSignUp($postObj,$keyword,$filename);
      $keyword='三人篮报名';
      $filename='log/sanrenlanSignUp.txt';
      self::responseSignUp($postObj,$keyword,$filename);
      $keyword='查看报名';
      $Content="主赛日：\r\n".self::readFile('log/zhusairiSignUp.txt');
      $Content.="三人篮：\r\n". self::readFile('log/sanrenlanSignUp.txt');
      self::responseText($postObj,$keyword,$Content);
    }


    public static function responseTuling($postObj){
      if (!self::$responded) {
        $toUser=$postObj->FromUserName;
        $fromUser=$postObj->ToUserName;
        $time=time();
        $MsgType='text';
        $text=$postObj->Content;
        settype($text,'string');
        if (preg_match('/\/\:/',$text)==0) {
          $weixinID=$postObj->FromUserName;
          $selfInfo = [
    		    'location' => [
       	  	'city' => '广州'
    		    ]
	        ];
          
          $apiKey=TULING_APIKEY;
          settype($apiKey,'string');
          $secret=TULING_SECRET;
          settype($secret,'string');
	        $data=new Tuling123($apiKey,$secret,$weixinID,$selfInfo);
	        $Content=$data->tuling($text);
          
         //$Content=TULING_APIKEY;
        } else {
          $Content=$postObj->Content;
        }
        $template=self::$textTemplate;
        $info=sprintf($template,$toUser,$fromUser,$time,$MsgType,$Content);
        echo $info;
        }
    }

    public static function responseDefaut2($postObj){
      $toUser=$postObj->FromUserName;
      $fromUser=$postObj->ToUserName;
      $time=time();
      $MsgType='text';
      $Content='这和我是个冷酷的复读机有什么关系呢？';
      $template=self::$textTemplate;
      $info=sprintf($template,$toUser,$fromUser,$time,$MsgType,$Content);
      echo $info;
    }

    public static function responseText($postObj,$keyword,$Content){
      if ($postObj->Content==$keyword) {
            $toUser=$postObj->FromUserName;
            $fromUser=$postObj->ToUserName;
            $time=time();
            $MsgType='text';
            $template=self::$textTemplate;
            $info=sprintf($template,$toUser,$fromUser,$time,$MsgType,$Content);
            self::$responded=true;
            echo $info;
          }
    }


    public static function responseAddDP($postObj){
      $weixinID=$postObj->FromUserName;
      if ($weixinID=="oKldy5yGdkZnvBJOKH8C7hYRql4U") {
        $str=$postObj->Content;
        $pattern=$pattern='/^加德育分 ([^ ]*?) ([^ ]*?) (.*)$/';
        if (preg_match($pattern,$str)<>0) {
          $point=preg_replace($pattern,'$1',$str);
          $info=preg_replace($pattern,'$2',$str);
          $names=preg_replace($pattern,'$3',$str);
          if (strtolower($names)=='all') $names=PDOc::getAllUsernames();
          $pattern='/([^ ]+)/';
          $arr=array();
          $X=preg_match_all($pattern,$names,$arr);
          $res=0;
          foreach ($arr[0] as $row) {
            $res+=PDOc::addDP($row,$point,$info);
          }
          $Content="Done.\r\n".$res." rows affected.";
          $toUser=$postObj->FromUserName;
          $fromUser=$postObj->ToUserName;
          $time=time();
          $MsgType='text';
          $template=self::$textTemplate;
          $info=sprintf($template,$toUser,$fromUser,$time,$MsgType,$Content);
          self::$responded=true;
          echo $info;
        }
      }
    }
          
    public static function responseDelDP($postObj){
      $weixinID=$postObj->FromUserName;
      if ($weixinID=="oKldy5yGdkZnvBJOKH8C7hYRql4U") {
        $str=$postObj->Content;
        $pattern=$pattern='/^删除 德育分 (.*)$/';
        if (preg_match($pattern,$str)<>0) {
          $IDs=preg_replace($pattern,'$1',$str);
          $pattern='/([^ ]+)/';
          $arr=array();
          $X=preg_match_all($pattern,$IDs,$arr);
          $res=0;
          foreach ($arr[0] as $row) {
            $res+=PDOc::delDP($row);
          }
          $Content="Done.\r\n".$res." rows affected.";
          $toUser=$postObj->FromUserName;
          $fromUser=$postObj->ToUserName;
          $time=time();
          $MsgType='text';
          $template=self::$textTemplate;
          $info=sprintf($template,$toUser,$fromUser,$time,$MsgType,$Content);
          self::$responded=true;
          echo $info;
        }
      }
    }

    public static function responseAddWP($postObj){
      $weixinID=$postObj->FromUserName;
      if ($weixinID=="oKldy5yGdkZnvBJOKH8C7hYRql4U") {
        $str=$postObj->Content;
        $pattern=$pattern='/^加文体分 ([^ ]*?) ([^ ]*?) (.*)$/';
        if (preg_match($pattern,$str)<>0) {
          $point=preg_replace($pattern,'$1',$str);
          $info=preg_replace($pattern,'$2',$str);
          $names=preg_replace($pattern,'$3',$str);
          if (strtolower($names)=='all') $names=PDOc::getAllUsernames();
          $pattern='/([^ ]+)/';
          $arr=array();
          $X=preg_match_all($pattern,$names,$arr);
          $res=0;
          foreach ($arr[0] as $row) {
            $res+=PDOc::addWP($row,$point,$info);
          }
          $Content="Done.\r\n".$res." rows affected.";
          $toUser=$postObj->FromUserName;
          $fromUser=$postObj->ToUserName;
          $time=time();
          $MsgType='text';
          $template=self::$textTemplate;
          $info=sprintf($template,$toUser,$fromUser,$time,$MsgType,$Content);
          self::$responded=true;
          echo $info;
        }
      }
    }
          
    public static function responseDelWP($postObj){
      $weixinID=$postObj->FromUserName;
      if ($weixinID=="oKldy5yGdkZnvBJOKH8C7hYRql4U") {
        $str=$postObj->Content;
        $pattern=$pattern='/^删除 文体分 (.*)$/';
        if (preg_match($pattern,$str)<>0) {
          $IDs=preg_replace($pattern,'$1',$str);
          $pattern='/([^ ]+)/';
          $arr=array();
          $X=preg_match_all($pattern,$IDs,$arr);
          $res='';
          foreach ($arr[0] as $row) {
            $res.=PDOc::delWP($row);
          }
          $Content="Done.\r\n".$res." rows affected.";
          $toUser=$postObj->FromUserName;
          $fromUser=$postObj->ToUserName;
          $time=time();
          $MsgType='text';
          $template=self::$textTemplate;
          $info=sprintf($template,$toUser,$fromUser,$time,$MsgType,$Content);
          self::$responded=true;
          echo $info;
        }
      }
    }
    
    public static function responseWeather($postObj){
      $keyword='天气2';
      if ($postObj->Content==$keyword) {
            $Content="天河:\n";
      	    $Content.=self::getWeather('101280109');
            $toUser=$postObj->FromUserName;
            $fromUser=$postObj->ToUserName;
            $time=time();
            $MsgType='text';
            $template=self::$textTemplate;
            $info=sprintf($template,$toUser,$fromUser,$time,$MsgType,$Content);
            self::$responded=true;
            echo $info;
      }
      $str=$postObj->Content;
      $pattern="/^天气2 (.+)$/";
      $replacement='$1';
      if (preg_match($pattern,$str)<>0) {
            $keyword=preg_replace($pattern,$replacement,$str);
            $Content=$keyword.":\n";
      		 $cityID=self::getCityID($keyword);
            $Content.=($cityID=='N/A')?'No data.':self::getWeather($cityID);
            $toUser=$postObj->FromUserName;
            $fromUser=$postObj->ToUserName;
            $time=time();
            $MsgType='text';
            $template=self::$textTemplate;
            $info=sprintf($template,$toUser,$fromUser,$time,$MsgType,$Content);
            self::$responded=true;
            echo $info;
      }
    }
    
    public static function responseACWeather($postObj){
    	$keyword='天气';
      if ($postObj->Content==$keyword) {
            $Content="天河:\n";
      	    $Content.=self::getAcuteWeather('101280109');
            $toUser=$postObj->FromUserName;
            $fromUser=$postObj->ToUserName;
            $time=time();
            $MsgType='text';
            $template=self::$textTemplate;
            $info=sprintf($template,$toUser,$fromUser,$time,$MsgType,$Content);
            self::$responded=true;
            echo $info;
      }
      $str=$postObj->Content;
      $pattern="/^天气 (.+)$/";
      $replacement='$1';
      if (preg_match($pattern,$str)<>0) {
            $keyword=preg_replace($pattern,$replacement,$str);
            $Content=$keyword.":\n";
      		$cityID=self::getCityID($keyword);
            $Content.= ($cityID=='N/A')?'No data.': self::getAcuteWeather($cityID);
      		//$Content=$cityID;
            $toUser=$postObj->FromUserName;
            $fromUser=$postObj->ToUserName;
            $time=time();
            $MsgType='text';
            $template=self::$textTemplate;
            $info=sprintf($template,$toUser,$fromUser,$time,$MsgType,$Content);
            self::$responded=true;
            echo $info;
      }
    }
            
    public static function responseSignUp($postObj,$keyword,$filename){
      $weixinID=$postObj->FromUserName;
      if ($postObj->Content==$keyword) {
            if (self::checkIfSignUp($weixinID,$filename)) {
    					self::cancelSignUp($weixinID,$filename);
      				$Content='Cancelled.';
    			  } else {
      				file_put_contents($filename,PDOc::getUsername($weixinID)."\r\n",FILE_APPEND);
            		$Content='Done.';
      		  }
            $toUser=$postObj->FromUserName;
            $fromUser=$postObj->ToUserName;
            $time=time();
            $MsgType='text';
            $template=self::$textTemplate;
            $info=sprintf($template,$toUser,$fromUser,$time,$MsgType,$Content);
            self::$responded=true;
            echo $info;
          }
    }
            

    public static function responseFeedback($postObj){
      $Content='Recorded.';
      $keyword='反馈';
      $filename='log/feedback.txt';
      $pattern='/'.$keyword.' ([\s\S]+)/';
      $replacement='$1';
      if (preg_match($pattern,$postObj->Content)<>0) {
            if ($keyword.' '.preg_replace($pattern,$replacement,$postObj->Content)==$postObj->Content) {
                 $fileContent=PDOc::getUsername($postObj->FromUserName).' '.preg_replace($pattern, $replacement, $postObj->Content);
                 file_put_contents($filename,$fileContent."\r\n",FILE_APPEND);
                 $toUser=$postObj->FromUserName;
                 $fromUser=$postObj->ToUserName;
                 $time=time();
                 $MsgType='text';
                 $template=self::$textTemplate;
                 $info=sprintf($template,$toUser,$fromUser,$time,$MsgType,$Content);
                 self::$responded=true;
                 echo $info;
            }
          }
    }

    public static function responseClose($postObj){
      $keyword='解除';
      if ($postObj->Content==$keyword) {
        if (PDOc::checkWeixinIDExistence($postObj->FromUserName)) {
              PDOc::PDOdeleteUser($postObj->FromUserName);
              $Content='Done.';
            } else {
              $Content='Nothing to do.';
            }
            $toUser=$postObj->FromUserName;
            $fromUser=$postObj->ToUserName;
            $time=time();
            $MsgType='text';
            $template=self::$textTemplate;
            $info=sprintf($template,$toUser,$fromUser,$time,$MsgType,$Content);
            self::$responded=true;
            echo $info;
      }
    }

    public static function responseRegister($postObj){
      $keyword='绑定';
      $pattern='/'.$keyword.' ([\s\S]+)/';
      $replacement='$1';
      if (preg_match($pattern,$postObj->Content)<>0) {
            $username=preg_replace($pattern, $replacement, $postObj->Content);
            if (PDOc::checkWeixinIDExistence($postObj->FromUserName)) {
              $Content='Nothing to do.';
            } else {
              PDOc::PDOinsertUser($username,$postObj->FromUserName);
              $Content='Done.';
            }
            $toUser=$postObj->FromUserName;
            $fromUser=$postObj->ToUserName;
            $time=time();
            $MsgType='text';
            $template=self::$textTemplate;
            $info=sprintf($template,$toUser,$fromUser,$time,$MsgType,$Content);
            self::$responded=true;
            echo $info;
          }
    }
            
    public static function responseTranslate($postObj){
      $str=$postObj->Content;
      $pattern="/^翻译 (.+)$/";
      $replacement='$1';
      if (preg_match($pattern,$str)<>0) {
            $keyword=preg_replace($pattern,$replacement,$str);
            $Content=self::getTranslate($keyword);
            $toUser=$postObj->FromUserName;
            $fromUser=$postObj->ToUserName;
            $time=time();
            $MsgType='text';
            $template=self::$textTemplate;
            $info=sprintf($template,$toUser,$fromUser,$time,$MsgType,$Content);
            self::$responded=true;
            echo $info;
          }
    }
    
            
    public static function responseBaike($postObj){
      $str=$postObj->Content;
      $pattern="/^百科 (.+)$/";
      $replacement='$1';
      if (preg_match($pattern,$str)<>0) {
            $keyword=preg_replace($pattern,$replacement,$str);
            $Content=self::getBaike($keyword);
            $toUser=$postObj->FromUserName;
            $fromUser=$postObj->ToUserName;
            $time=time();
            $MsgType='text';
            $template=self::$textTemplate;
            $info=sprintf($template,$toUser,$fromUser,$time,$MsgType,$Content);
            self::$responded=true;
            echo $info;
          }
    }
            
    public static function responseGetUserInfo($postObj){
      $str=$postObj->Content;
      $pattern='/^查询/';
      if (preg_match($pattern,$str)<>0) {
        $toUser=$postObj->FromUserName;
        $fromUser=$postObj->ToUserName;
        if ($toUser=='oKldy5yGdkZnvBJOKH8C7hYRql4U' or $toUser=='oKldy561oiZ45NTIBeHKCv0MNn28' or $toUser=='oKldy5wCd1sbJ8CjZnaX1u5OvR4c' or $toUser=='oKldy56d0F7jhWrEFjjSx4vynQFQ') {
    			$Content=PDOc::superGUI($postObj);
    		} else {
      		$Content=PDOc::GUI($postObj);
      	}
        $time=time();
        $MsgType='text';
        $template=self::$textTemplate;
        $info=sprintf($template,$toUser,$fromUser,$time,$MsgType,$Content);
        self::$responded=true;
        echo $info;
      }
    }
            
            
    public static function checkIfSignUp($weixinID,$filename){
      $username=PDOc::getUsername($weixinID);
      file_put_contents($filename,'',FILE_APPEND);
    	 $handle=fopen($filename,'r');
      while (!feof($handle)) {
      	$str=fgets($handle);
      	//file_put_contents('log/test2.txt',$str,FILE_APPEND);
      	if ($str==($username."\r\n")) return true;   
      }
      return false;
    }
        
    public static function cancelSignUp($weixinID,$filename){
      $username=PDOc::getUsername($weixinID);
      file_put_contents($filename,'',FILE_APPEND);
    	 $handle=fopen($filename,'r');
    	$str='';
      while (!feof($handle)) {
      	$temp=fgets($handle);
      	//file_put_contents('log/test2.txt',$str,FILE_APPEND);
      	if ($temp<>($username."\r\n")) $str.=$temp;   
      }
      file_put_contents($filename,$str);
    }


    public static function timeTable(){
      $Content="下一节课是:\n".self::nextClass();
      return self::readFile('contents/timetable.txt');
    }


    public static function nextClass(){
      $handle=fopen('contents/timetabledata.txt','r');
      $table=array();
      while (!feof($handle)) {
          $str=fgets($handle);
          if (preg_match('/~~/',$str)==0) {
              $pattern='/^([0-9]{5}) (.*)\r\n$/';
              $t='$1';
              $content='$2';
              $t=preg_replace($pattern,$t,$str);
              $content=preg_replace($pattern,$content,$str);
              $table[$t]=$content;
              }
      }
      $nowtime=date("NHi",time());
      settype($nowtime,"int");
      foreach ($table as $classtime => $Content) {
        settype($classtime,"int"); 
        if ($classtime>$nowtime) {
          return $Content;
        }
      }
      return "Failed.";
    }
      
      
    public static function getCityID($cityname){
      $handle=fopen('contents/country.txt','r');
      $replacement='$1';
      while (!feof($handle)) {
      	$str=urlencode(fgets($handle));
        $pattern='/^.*'.urlencode(',').urlencode($cityname).urlencode(',').'([0-9]+).*$/';
      	if (preg_match($pattern,$str)) return preg_replace($pattern,$replacement,$str);
      	$pattern='/^.*'.urlencode(',').urlencode($cityname).urlencode(',城区,').'([0-9]+).*$/';
      	if (preg_match($pattern,$str)) return preg_replace($pattern,$replacement,$str);
        $pattern='/'.urlencode($cityname).urlencode(',城区,').'([0-9]+).*$/';
      	if (preg_match($pattern,$str)) return preg_replace($pattern,$replacement,$str);
      }
      return 'N/A';
    }
      
      
    
    public static function getWeather($id){
   	$data=file_get_contents('http://www.weather.com.cn/weather/'.$id.'.shtml');
   	file_put_contents('contents/weather.shtml',$data);
   	$handle=fopen('contents/weather.shtml','r');
   	while (!feof($handle)) {
   		$str=fgets($handle);
   		if (preg_match('<ul class="t clearfix">',$str)<>0) break;
   	}
   	$i=0;
   	fgets($handle);
   	$res='';
   	while ($i<=17*3-2) {
   		$str=fgets($handle);
   		$i++;
      		$pattern="/<h1>([\s\S]+)<\/h1>/";
      		if (preg_match($pattern,$str)<>0) $res.=preg_replace($pattern,'$1',$str);
      		$pattern='/<p title="([\s\S]+?)".*/';
      		if (preg_match($pattern,$str)<>0) $res.=preg_replace($pattern,'$1',$str);
      		$pattern='/^(?!s)<i>([\s\S]+?)<\/i>/';
      		if (preg_match($pattern,$str)<>0) $res.=preg_replace($pattern,'$1',$str);
      		$pattern='/<span>([\s\S]+?)<\/span>\/<i>([\s\S]+?)<\/i>/';
      		if (preg_match($pattern,$str)<>0) $res.=preg_replace($pattern,'$1~$2',$str);
    	}
    	return $res;
    }
    
    public static function getAcuteWeather($id){
   	$data=file_get_contents('http://www.weather.com.cn/weather1d/'.$id.'.shtml');
   	//return 'http://www.weather.com.cn/weather1d/'.$id.'.shtml';
   	file_put_contents('contents/weather.shtml',$data);
   	$handle=fopen('contents/weather.shtml','r');
   	while (!feof($handle)) {
   		$str=fgets($handle);
   		if (preg_match('/var hour3data/',$str)<>0) {
   			$temp=$str;
   			break;
   		}
   	}
   	preg_match_all('/\[([^\[\]]+)\]/',$temp,$match);
   	foreach ($match as $key => $arr) {
   		$temp=$arr[0];
   		break;
  	 }
   	preg_match_all('/\"([^\"\"]+)\"/',$temp,$match);
    	foreach ($match as $key => $arr) {
   		$res='';
   		foreach ($arr as $k => $v) {
   			$pattern='/"(.*),(.*),(.*),(.*),(.*),(.*),(.*)"/';
   			$replacement='$1,$3,$4,$6';
   			$res.=preg_replace($pattern,$replacement,$v);
   			$res.="\n";
   		}
   		break;
   	}
   	return $res;
    }
            
   public static function getTranslate($keyword){
   $res='';
   $url='http://cn.bing.com/dict/search?q='.urlencode($keyword);
   $html=file_get_html($url);
   //return 0;
   foreach($html->find('span.pos,span.def,div.no_results') as $row) {
       //return 0;
   	$res.=$row->plaintext;
       $res.="\n";
   }
   //return 0;
   $html->clear();
   return $res;
   }
   
   public static function getNews(){
   	$url='http://news.163.com/pad/';
   	$html=file_get_html($url);
   	$res='';
   	$i=0;
   	foreach($html->find('div.mod_top_news2') as $row) {
  			foreach($row->find('a') as $key){
  			$i++;
       		$res.=$i.'. '.$key."\r\n";
   			/*
    			echo $key->plaintext;
       		echo '<br/>';
       		echo $key->href;
       		echo '<br/>';
   			*/
       	}
   	}
   	$html->clear();
    	$res.='慢慢看哦~';
    	return $res;
   }
   
   public static function getBaike($keyword){
   $res='';
   $url='https://baike.baidu.com/item/'.urlencode($keyword);
   $html=file_get_html($url);
   //return 0;
   foreach($html->find('div.lemma-summary,div.sorryBox') as $row) {
       //return 0;
   	$res.=$row->plaintext;
       $res.="\n";
   }
   //return 0;
   $res.=$url;
   $res=preg_replace("/\[(.+?)\]&nbsp;/",'',$res);
   $res=preg_replace('/                                    /','',$res);
   $html->clear();
   return $res;
   }

   

    public static function record($postObj){
      $filename='log/log4.txt';
      $fileContent=date("Y-m-d H:i:s",time()).' '.PDOc::getUsername($postObj->FromUserName).' '.$postObj->Content;
      file_put_contents($filename,$fileContent."\r\n",FILE_APPEND);
    }

    public static function readFile($filename){
      $handle=fopen($filename,'r');
      $str='';
      while (!feof($handle)) {
        $tmp=fgets($handle);
        $pattern='/^~~/';
        if (preg_match($pattern,$tmp)==0) $str.=$tmp;
      }
      return $str;
    }

  }
?>