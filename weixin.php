<?php
require('PDO.php');
require('simple_html_dom.php');
require('Tuling123.php');
require('settings.php');
require('vote.php');
PDOc::_connect();
  class weixin {
      
  
    public static $textTemplate="
        <xml> 
            <ToUserName><![CDATA[%s]]></ToUserName> 
            <FromUserName><![CDATA[%s]]></FromUserName> 
            <CreateTime>%s</CreateTime> 
            <MsgType><![CDATA[%s]]></MsgType> 
            <Content><![CDATA[%s]]></Content> 
        </xml>";

    public static $musicTemplate="
        <xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Music>
                <Title><![CDATA[%s]]></Title>
                <Description><![CDATA[%s]]></Description>
                <MusicUrl><![CDATA[%s]]></MusicUrl>
                <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
                <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
            </Music>
        </xml>";
        
    public static $newsTemplate="
        <xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <ArticleCount>1</ArticleCount>
            <Articles>
                <item>
                    <Title><![CDATA[%s]]></Title> 
                    <Description><![CDATA[%s]]></Description>
                    <PicUrl><![CDATA[%s]]></PicUrl>
                    <Url><![CDATA[%s]]></Url>
                </item>
            </Articles>
        </xml>";

    public static $picTemplate="
        <xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Image>
                <MediaId><![CDATA[%s]]></MediaId>
            </Image>
        </xml>";

    public static $responded=false;
    
    
    public static function getAccessToken(){
        $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.APPID.'&secret='.APPSECRET;
        $json=file_get_contents($url);
        $arr=json_decode($json,TRUE);
        return $arr['access_token'];
    }
    
    public static function uploadThumb(){
        $type="thumb"; 
        //$data=file_get_contents($url);
        //file_put_contents($filename,$data);
        $filedata=array("thumb"=>"@pic/thumb.jpg");
        $url="https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".self::getAccessToken()."&type=".$type;
        $curl=curl_init();
        curl_setopt($curl,CURLOPT_URL,$url);
        if (!empty($filedata)){
            curl_setopt($curl,CURLOPT_POST,TRUE);
            curl_setopt($curl,CURLOPT_POSTFIELDS,$filedata);
        }
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
        $json=curl_exec($curl);
        curl_close($curl);
        $arr=json_decode($json,TRUE);
        return $arr['thumb_media_id'];
        //return $json;
    }

    public static function uploadPic($filename){
        $type="image"; 
        //$data=file_get_contents($url);
        //file_put_contents($filename,$data);
        $filedata=array("image"=>"@pic/".$filename);
        $url="https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".self::getAccessToken()."&type=".$type;
        $curl=curl_init();
        curl_setopt($curl,CURLOPT_URL,$url);
        if (!empty($filedata)){
            curl_setopt($curl,CURLOPT_POST,TRUE);
            curl_setopt($curl,CURLOPT_POSTFIELDS,$filedata);
        }
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
        $json=curl_exec($curl);
        curl_close($curl);
        $arr=json_decode($json,TRUE);
        return $arr['media_id'];
        //return $json;
    }
  
    public static function responseSubscribe($postObj){
      if (strtolower($postObj->Event=='subscribe')){
          $toUser=$postObj->FromUserName;
          $fromUser=$postObj->ToUserName;
          $time=time();
          $MsgType='text';
          $Content='欢迎！输入“使用手册”可以查看本公众号的功能！';
          $template=self::$textTemplate;
          $info=sprintf($template,$toUser,$fromUser,$time,$MsgType,$Content);
          echo $info;
          //file_put_contents('log.txt',$info);
        }
    }

          

    public static function responseKeyWords($postObj){
      /*
      $keyword='关键字';
      $Content=self::readFile('contents/keywords.txt');
      self::responseText($postObj,$keyword,$Content);
      $keyword='关键词';
      $Content=self::readFile('contents/keywords.txt');
      self::responseText($postObj,$keyword,$Content);
      */
      self::responseInstructions($postObj);
      $keyword='教材';
      $Content=self::readFile('contents/textbooks.txt');
      self::responseText($postObj,$keyword,$Content);
      /*
      $keyword='课程表';
      $Content="下一节课是:\n".self::nextClass()."\n\n".self::timeTable();
      self::responseText($postObj,$keyword,$Content);
      $keyword='课表';
      $Content="下一节课是:\n".self::nextClass()."\n\n".self::timeTable();
      self::responseText($postObj,$keyword,$Content);
      $keyword='考试';
      $Content="下一场考试是:\n".self::nextClass()."\n\n".self::timeTable();
      self::responseText($postObj,$keyword,$Content);
      */
      $keyword='软件';
      $Content=self::readFile('contents/software.txt');
      self::responseText($postObj,$keyword,$Content);
      $keyword='查看所有投票';
      $Content=vote::find_all_votes();
      $Content=($Content == array())? 'No result.': implode(', ', $Content);
      self::responseText($postObj,$keyword,$Content);
      $keyword='作业';
      $Content=self::readFile('contents/homework.txt');
      self::responseText($postObj,$keyword,$Content);
      $keyword='作业栏';
      $Content=self::readFile('contents/homework.txt');
      self::responseText($postObj,$keyword,$Content);
      /*
      $keyword='下一节课';
      $Content=self::nextClass();
      self::responseText($postObj,$keyword,$Content);
      $keyword='下节课';
      $Content=self::nextClass();
      self::responseText($postObj,$keyword,$Content);
      */
      $keyword='新闻';
      $Content=self::getNews();
      self::responseText($postObj,$keyword,$Content);
      //$keyword='test';
      //$Content=self::uploadPic('test.jpg');
      //self::responseText($postObj,$keyword,$Content);
      $pic = 'pay_QRcode.jpg';
      $keyword = '付款';
      self::responsePic($postObj, $keyword, $pic);
      self::responseMusic($postObj);
      self::responseRefleshLog($postObj);
      self::responseGetUserInfo($postObj);
      self::responseWeather($postObj);
      self::responseACWeather($postObj);
      self::responseTranslate($postObj);
      self::responseBaike($postObj);
      self::responseFeedback($postObj);
      //self::responseBaobei($postObj);
      self::responseAddDP($postObj);
      self::responseAddWP($postObj);
      self::responseDelDP($postObj);
      self::responseDelWP($postObj);
      self::responseVote($postObj);
      self::responseUnvote($postObj);
      self::responseViewVote($postObj);
      self::responseCreateVote($postObj);
      self::responseDeleteVote($postObj);
      //self::responseRegister($postObj);
      //self::responseClose($postObj);
      //$keyword='查看报名';
      //$Content="出游：\r\n".self::readFile('log/chuyouSignUp.txt');
      //self::responseText($postObj,$keyword,$Content);
    }

    
    public static function randSong($postObj,$filename){
        $json=file_get_contents($filename);
        $arr=json_decode($json,TRUE);
        $break=rand(1,count($arr['Body']));
        $i=0;
        foreach ($arr['Body'] as $song) {
            $i++;
            if ($i>$break) {
                $res=$song;
                break;
            }
        }
        $toUser=$postObj->FromUserName;
        $fromUser=$postObj->ToUserName;
        $time=time();
        $MsgType='music';
        $template=self::$musicTemplate;
        $TITLE=$res['title'];
        $DESCRIPTION=$res['author'];
        $MUSIC_Url='http://music.163.com/song/media/outer/url?id='.$res['id'].'.mp3';
        $HQ_MUSIC_Url='http://music.163.com/song/media/outer/url?id='.$res['id'].'.mp3';
        $media_id=self::uploadThumb();
        $info=sprintf($template,$toUser,$fromUser,$time,$MsgType,$TITLE,$DESCRIPTION,$MUSIC_Url,$HQ_MUSIC_Url,$media_id);
        echo $info;
    }



    public static function findSong($postObj,$songname){
        $url='https://api.mlwei.com/music/api/wy/?key=523077333&id='.urlencode($songname).'&type=so&cache=0&nu=1';
        $json=file_get_contents($url);
        $arr=json_decode($json,TRUE);
        $break=0;
        $i=0;
        foreach ($arr['Body'] as $song) {
            $i++;
            if ($i>$break) {
                $res=$song;
                break;
            }
        }
        $toUser=$postObj->FromUserName;
        $fromUser=$postObj->ToUserName;
        $time=time();
        $MsgType='music';
        $template=self::$musicTemplate;
        $TITLE=$res['title'];
        $DESCRIPTION=$res['author'];
        $MUSIC_Url='http://music.163.com/song/media/outer/url?id='.$res['id'].'.mp3';
        $HQ_MUSIC_Url='http://music.163.com/song/media/outer/url?id='.$res['id'].'.mp3';
        $media_id=self::uploadThumb();
        $info=sprintf($template,$toUser,$fromUser,$time,$MsgType,$TITLE,$DESCRIPTION,$MUSIC_Url,$HQ_MUSIC_Url,$media_id);
        echo $info;
    }


    public static function responseMusic($postObj){
        $str=$postObj->Content;
        $pattern='/^音乐/';
        
        if (preg_match($pattern,$str)<>0) {
            $pattern='/^音乐 华语$/';
            if (preg_match($pattern,$str)<>0) {
                $filename='songlist/songlist_huayu.json';
                self::randSong($postObj,$filename);
                self::$responded=true;
            }
            $pattern='/^音乐 日语$/';
            if (preg_match($pattern,$str)<>0) {
                $filename='songlist/songlist_riyu.json';
                self::randSong($postObj,$filename);
                self::$responded=true;
            }
            $pattern='/^音乐 韩语$/';
            if (preg_match($pattern,$str)<>0) {
                $filename='songlist/songlist_hanyu.json';
                self::randSong($postObj,$filename);
                self::$responded=true;
            }
            $pattern='/^音乐 英语$/';
            if (preg_match($pattern,$str)<>0) {
                $filename='songlist/songlist_yingyu.json';
                self::randSong($postObj,$filename);
                self::$responded=true;
            }
            $pattern='/^音乐 acg$/';
            if (preg_match($pattern,$str)<>0) {
                $filename='songlist/songlist_acg.json';
                self::randSong($postObj,$filename);
                self::$responded=true;
            }
            $pattern='/^音乐 鬼畜$/';
            if (preg_match($pattern,$str)<>0) {
                $filename='songlist/songlist_guichu.json';
                self::randSong($postObj,$filename);
                self::$responded=true;
            }
            $pattern='/^音乐 粤语$/';
            if (preg_match($pattern,$str)<>0) {
                $filename='songlist/songlist_yueyu.json';
                self::randSong($postObj,$filename);
                self::$responded=true;
            }
            $pattern='/^音乐 v家$/i';
            if (preg_match($pattern,$str)<>0) {
                $filename='songlist/songlist_vocaloid.json';
                self::randSong($postObj,$filename);
                self::$responded=true;
            }
            $pattern='/^音乐 轻音乐$/';
            if (preg_match($pattern,$str)<>0) {
                $filename='songlist/songlist_qing.json';
                self::randSong($postObj,$filename);
                self::$responded=true;
            }
            $pattern='/^音乐$/';
            if (preg_match($pattern,$str)<>0) {
                $filename='songlist/mySonglist.json';
                self::randSong($postObj,$filename);
                self::$responded=true;
            }
            $Content='关键词有：华语、日语、韩语、英语、粤语、acg、鬼畜、v家和轻音乐哦！';
            $toUser=$postObj->FromUserName;
            $fromUser=$postObj->ToUserName;
            $time=time();
            $MsgType='text';
            $template=self::$textTemplate;
            $info=sprintf($template,$toUser,$fromUser,$time,$MsgType,$Content);
            self::$responded=true;
            echo $info;
        }
        $pattern='/^点歌 (.*)$/';
        if (preg_match($pattern,$str)<>0) {
            $songname=preg_replace($pattern,'$1',$str);
            self::findSong($postObj,$songname);
            self::$responded=true;
        }
        
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

    public static function responseRefleshLog($postObj){
      if ($postObj->Content=='刷新日志'){
        $res=PDOc::refleshLog();
        $toUser=$postObj->FromUserName;
        $fromUser=$postObj->ToUserName;
        $time=time();
        $MsgType='text';
        if ($res==true) {
          $Content='Done.';
        } else {
          $Content='Failed.';
        }
        $template=self::$textTemplate;
        $info=sprintf($template,$toUser,$fromUser,$time,$MsgType,$Content);
        echo $info;
      }
    }

    public static function responsePic($postObj, $keyword, $pic){
      if ($postObj->Content==$keyword){
        $toUser=$postObj->FromUserName;
        $fromUser=$postObj->ToUserName;
        $time=time();
        $MsgType='image';
        $media_id=self::uploadPic($pic);
        $template=self::$picTemplate;
        $info=sprintf($template,$toUser,$fromUser,$time,$MsgType,$media_id);
        echo $info;
      }
    }
        
    public static function responseInstructions($postObj){
        if ($postObj->Content=='使用手册') {
            $toUser=$postObj->FromUserName;
            $fromUser=$postObj->ToUserName;
            $time=time();
            $MsgType='news';
            $Title='复读姬使用手册';
            $Description='给不会使用这个公众号的你——';
            $PicUrl='http://mmbiz.qpic.cn/mmbiz_jpg/NtzSic72E5kDJ213QWXw2eKI7QRkV3EkwUia9RaMH7nKyIVsm8AvUKROlq3LpnMs78UltnV2Lpg7AIEx4T6DuBGA/0?wx_fmt=jpeg';
            $Url='https://mp.weixin.qq.com/s/THWKnDCSp6Do57q5kGQpOg';
            $template=self::$newsTemplate;
            $info=sprintf($template,$toUser,$fromUser,$time,$MsgType,$Title,$Description,$PicUrl,$Url);
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
     
    /*       
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
    */       

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

    public static function responseBaobei($postObj){
      $Content='Recorded.';
      $keyword='报备';
      $filename='log/baobei.txt';
      $pattern='/'.$keyword.' (.+)/';
      $replacement='$1';
      if (preg_match($pattern,$postObj->Content)<>0) {
          if ($keyword.' '.preg_replace($pattern,$replacement,$postObj->Content)==$postObj->Content) {
                 $toUser=$postObj->FromUserName;
                 $fromUser=$postObj->ToUserName;
                 if (PDOc::checkWeixinIDExistence($postObj->FromUserName)) {
                   
                 	 $studentID=PDOc::getStudentID($postObj->FromUserName);
                 	 $name=PDOc::getUsername($postObj->FromUserName);
                 	 $phone=PDOc::getPhone($postObj->FromUserName);
                 	 $parentPhone=PDOc::getParentPhone($postObj->FromUserName);
                 	 $sex=PDOc::getSex($postObj->FromUserName);
                 	 $fileContent=strval($studentID).' '.strval($name).' '.strval($sex).' '.strval($phone).' '.strval($parentPhone).' '.preg_replace($pattern,$replacement,$postObj->Content);
                   //$Content=$fileContent;
                   file_put_contents($filename,$fileContent."\r\n",FILE_APPEND);

                 } else {
                 	 $Content='Failed.';
                 }
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
            
    public static function responseCreateVote($postObj){
      $keyword='发起投票';
      $pattern='/^'.$keyword.' ([\s\S]+)/';
      $replacement='$1';
      if (preg_match($pattern,$postObj->Content)<>0) {
            $class = preg_replace($pattern, $replacement, $postObj->Content);
            if (!PDOc::checkWeixinIDExistence($postObj->FromUserName)) {
              $Content = 'Nothing to do.';
            } else {
              if (in_array(vote::find_all_votes(), $class)) {
                $Content = 'Fatel error: Vote exists.';
              } else {
                $obj_vote = new vote(PDOc::getUsername($postObj->FromUserName), $class);
                $obj_vote->save();
                $Content='Done.';
              }
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
              
    public static function responseVote($postObj){
      $keyword='投票';
      $pattern='/^'.$keyword.' ([\s\S]+)/';
      $replacement='$1';
      if (preg_match($pattern,$postObj->Content)<>0) {
            $class = preg_replace($pattern, $replacement, $postObj->Content);
            if (!PDOc::checkWeixinIDExistence($postObj->FromUserName)) {
              $Content='Nothing to do.';
            } else {
              $username = PDOc::getUsername($postObj->FromUserName);
              $obj_vote = new vote($username, $class);
              $status = $obj_vote->push();
              if ($status) $obj_vote->save();
              $Content = ($status)? 'Done.': 'Fatal error: Vote does not exist OR Element exists.';
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
              
    public static function responseDeleteVote($postObj){
      $keyword='删除投票';
      $pattern='/^'.$keyword.' ([\s\S]+)/';
      $replacement='$1';
      if (preg_match($pattern,$postObj->Content)<>0) {
            $class = preg_replace($pattern, $replacement, $postObj->Content);
            if (!PDOc::checkWeixinIDExistence($postObj->FromUserName)) {
              $Content='Nothing to do.';
            } else {
              $username = PDOc::getUsername($postObj->FromUserName);
              $obj_vote = new vote($username, $class);
              $status  = $obj_vote->destroy();
              $Content = ($status)? 'Done.': 'Fatal error: Vote does not exists OR Permission denied.';
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
              
    public static function responseUnvote($postObj){
      $keyword='撤销投票';
      $pattern='/^'.$keyword.' ([\s\S]+)/';
      $replacement='$1';
      if (preg_match($pattern,$postObj->Content)<>0) {
            $class = preg_replace($pattern, $replacement, $postObj->Content);
            if (!PDOc::checkWeixinIDExistence($postObj->FromUserName)) {
              $Content='Nothing to do.';
            } else {
              $username = PDOc::getUsername($postObj->FromUserName);
              $obj_vote = new vote($username, $class);
              $status = $obj_vote->pop();
              if ($status) $obj_vote->save();
              $Content = ($status)? 'Done.': 'Fatal error: Element OR Vote does not exist.';
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
              
    public static function responseViewVote($postObj){
      $keyword='查看投票';
      $pattern='/^'.$keyword.' ([\s\S]+)/';
      $replacement='$1';
      if (preg_match($pattern,$postObj->Content)<>0) {
            $class = preg_replace($pattern, $replacement, $postObj->Content);
            if (!PDOc::checkWeixinIDExistence($postObj->FromUserName)) {
              $Content='Nothing to do.';
            } else {
              if (vote::vote_exists($class)) {
                $username = PDOc::getUsername($postObj->FromUserName);
                $obj_vote = new vote($username, $class);
                $Content  = '发起人: '.$obj_vote->creator."\r\n".count($obj_vote->list).'人: ';
                //$Content .= $obj_vote->json();
                $Content .= (implode(', ',$obj_vote->list) == '')?'no member': implode(', ',$obj_vote->list);
              } else {
                $Content = 'Fatal error: Vote does not exist.';
              }
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
            
    /*
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
    */

    public static function timeTable(){
      $Content="下一场考试是:\n".self::nextClass();
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