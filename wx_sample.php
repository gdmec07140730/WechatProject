<?php
//装载模板文件
include_once("wx_tpl.php");
include_once("config.php");
//获取微信发送数据
$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

  //返回回复数据
if (!empty($postStr)){
          
    	//解析数据
          $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
    	//发送消息方ID
          $fromUsername = $postObj->FromUserName;
    	//接收消息方ID
          $toUsername = $postObj->ToUserName;
   	 //消息类型
          $form_MsgType = $postObj->MsgType;
          
  	//文字消息
          if($form_MsgType=="text")
          {
              
           //获取用户发送的文字内容
            $form_Content = trim($postObj->Content);
              
              
	   //如果发送内容不是空白回复用户相同的文字内容
 	    if(!empty($form_Content))
            {
            
              //回复菜谱类别
                if($form_Content=="lt")
                {
                  $msgType = "text";
                  $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, "现在就在聊天啦！");
                  echo $resultStr;
                  exit;                                   
                
                }
                
                
                if($form_Content=="ip")
                {
                    $IP = $_SERVER["REMOTE_ADDR"];
                    //获取IP的信息
                    $file_contents = file_get_contents("http://opendata.baidu.com/api.php?query=" . $IP . "&co=&resource_id=6006&t=1329357746681&ie=utf8&oe=utf8&format=json&tn=baidu");
                    //强制转换成数组
                    $data = json_decode($file_contents,true);
                    //信息的拼接
                    $return_str =  "你的location：" . $data['data'][0]['location'] . "\n" . 
                                "你的origip：" . $data['data'][0]['origip'] . "\n" . 
                              "你的origipquery：" . $data['data'][0]['origipquery'];
                    $msgType = "text";
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $return_str);
                    echo $resultStr;
                    exit;   
                    
                }
              //回复菜谱详情 外婆烧
              	if($form_Content=="wb")
                {
                  $resultStr="<xml>\n
                  <ToUserName><![CDATA[".$fromUsername."]]></ToUserName>\n
                  <FromUserName><![CDATA[".$toUsername."]]></FromUserName>\n
                  <CreateTime>".time()."</CreateTime>\n
                  <MsgType><![CDATA[news]]></MsgType>\n
                  <ArticleCount>2</ArticleCount>\n
                  <Articles>\n";                  
                  //菜谱详情数组  
                  $return_arr=array(
                  	array(
                        "文本管理",
                        "https://ss0.bdstatic.com/5aV1bjqh_Q23odCf/static/superman/img/logo/bd_logo1_31bdc765.png",
                        "http://mqt.tingin.cn/WechatSDK/index.php/Home/Text/getlist.html"
                        ),
                  	array(
                        "文本管理",
                        "https://ss0.bdstatic.com/5aV1bjqh_Q23odCf/static/superman/img/logo/bd_logo1_31bdc765.png",
                        "http://mqt.tingin.cn/WechatSDK/index.php/Home/Text/getlist.html"
                        ),
                  	array(
                        "文本管理",
                        "https://ss0.bdstatic.com/5aV1bjqh_Q23odCf/static/superman/img/logo/bd_logo1_31bdc765.png",
                        "http://mqt.tingin.cn/WechatSDK/index.php/Home/Text/getlist.html"
                        )
                  
                  );
                  //数组循环转化
                  foreach($return_arr as $value)
                  {
                    $resultStr.="<item>\n
                    <Title><![CDATA[".$value[0]."]]></Title> \n
                    <Description><![CDATA[]]></Description>\n
                    <PicUrl><![CDATA[".$value[1]."]]></PicUrl>\n
                    <Url><![CDATA[".$value[2]."]]></Url>\n
                    </item>\n";
                  }
                  $resultStr.="</Articles>\n
                  <FuncFlag>0</FuncFlag>\n
                  </xml>";                
                  echo $resultStr;
                  exit;
                }
                
                if($form_Content=="xx")
                {
                    $msgType = "text";
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, "以“问：XX答：XX”的形式发送就可以让微信学习你说话啦~\n“XX”就是你输入的内容哦~\n想怎么聊天自己DIY~");
                    echo $resultStr;
                    exit;
                }
                
              //表情回复歌曲
              if($form_Content=="tg")
              {
              	
	        	$msgType = "music";
                $resultStr = sprintf(
               		 $musicTpl, 
                         $fromUsername, 
                         $toUsername, 
                         $time, 
                         $msgType, 
                         "我的歌声里",
                         "曲婉婷",
                         "http://weixincourse-weixincourse.stor.sinaapp.com/mysongs.aac",
                         "http://weixincourse-weixincourse.stor.sinaapp.com/mysongs.mp3");
                echo $resultStr;
                exit;
							            
              }
              
              //默认回复
                $msgType = "text";
                $find = "select * from answer where problem=\"$form_Content\"";
                $result = mysql_query($find);
                $ltresult = mysql_fetch_array($result);
                $answer=$ltresult['answer'];
                if($answer!=null)
                {
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $answer);
                  echo $resultStr;
                  exit;
                }else{
                    $res = substr($form_Content,0,6);
                    $answer_with_text = strstr($form_Content,"答：");
                    $answer = substr($answer_with_text,6);
                    $problem_with_text = substr($form_Content,6);
                    $problem = substr($problem_with_text,0,strlen($problem_with_text)-strlen($answer)-6);
                    if($res == "问："){
                        $find = "select * from answer where problem=\"$problem\"";
                        $result = mysql_query($find);
                        $ltresult = mysql_fetch_array($result);
                        $data=$ltresult['answer'];
                        if($data!=null){
                            $resultStr=sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, "已经存在这个对话咯，换个问题试试？");
                        echo $resultStr;
                        exit;
                        }else{
                            require 'config.php';
                            $insert="insert into answer(problem,answer) values('$problem','$answer')";
                            @mysql_query($insert) or die('新增错误：'.mysql_error());
                            $return_str="你问的问题是：" . $problem . "\n" . "你的答案是：" . $answer . "\n" . "你的问答已录入数据库，即刻可进行问答聊天。不信你可以试试看~";
                            $resultStr=sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $return_str);
                            echo $resultStr;
                            exit;
                        }
                        
                    }else{
                        $res = mysql_query("select * from answer");
                        $data="";
                        while ($row = mysql_fetch_array($res)) {
                        	$str = $row['answer'].",";
                        	$data = $data.$str;
                        
                        }
                        $arr = explode(",",$data);
                        $result=array_rand($arr,1);
                        $return_str = $arr[$result];
                        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $return_str);
                        echo $resultStr;
                        exit;
                    }
                    
                }
                 
            }
            //否则提示输入
            else
            {
                $return_str="请输入字母编码进入功能：\n\n";
                $return_arr=array("lt.聊天\n","ip.查询IP\n","wb.文本管理\n","xx.学习功能\n","tg.听歌");
                $return_str.=implode("",$return_arr);
                $msgType = "text";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $return_str);
                echo $resultStr;
                exit;
            }          
          }
        
          
    	//事件消息
          if($form_MsgType=="event")
          {
            //获取事件类型
            $form_Event = $postObj->Event;
            //订阅事件
            if($form_Event=="subscribe")
            {
            $return_str="感谢您关注！[愉快]\n\n随便发信息跟我来互动吧~[玫瑰]\n也可以输入以下字母编码进入功能：\n\n";
            $return_arr=array("lt.聊天\n","ip.查询IP\n","wb.文本管理\n","xx.学习功能\n","tg.听歌");
            $return_str.=implode("",$return_arr);
            $msgType = "text";
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $return_str);
            echo $resultStr;
            exit;
	      
            }
          
          }
          
  }
  else 
  {
          echo "";
          exit;
  }

?>