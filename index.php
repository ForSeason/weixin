<?php
header('Content-Type:text/html;charset=utf-8');
date_default_timezone_set("Asia/Shanghai");
require('weixin.php');
    $timestamp = $_GET['timestamp'];
    $nonce = $_GET['nonce'];
    $token = 'scut18pie1';
    $signature = $_GET['signature'];
    $echostr   = $_GET['echostr']; 
    $array = array($timestamp,$nonce,$token);
    sort($array); 
    $tmpstr = implode('',$array);
    $tmpstr = sha1($tmpstr);   
    if($tmpstr == $signature  && $echostr){         
        echo $echostr;    
        exit;  
    }else{       
      $postArr=file_get_contents('php://input');
      file_put_contents('arr.txt', $postArr);
      $postObj=simplexml_load_string($postArr);
      //file_put_contents('arr.txt', $postObj);
      PDOc::record($postObj);
      if (strtolower($postObj->MsgType=='event')){
        weixin::responseSubscribe($postObj);
      }


      if (strtolower($postObj->MsgType)=='text') {
          weixin::responseKeyWords($postObj);
          weixin::responseTuling($postObj);     
      }

      if (strtolower($postObj->MsgType)=='image') {
          weixin::responseDefaut2($postObj);     
      }
    }

?>
