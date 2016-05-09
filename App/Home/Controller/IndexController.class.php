<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {

    public function index(){
        open(json_encode($_REQUEST).'-----'.json_encode($GLOBALS["HTTP_RAW_POST_DATA"]));
        if(checkSignature()){
            echo $_GET['echostr'];
            open('success');
          //  echo 'success';
            $xml = $GLOBALS["HTTP_RAW_POST_DATA"];
            $postObj = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;

            $toUsername = $postObj->ToUserName;

            $keyword = trim($postObj->Content);
            $msgType = $postObj->MsgType;
            $time = time();
            if($msgType == 'text'){
                $textTpl = msgText();
                $contentStr = '嘿嘿';

                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);

                echo $resultStr;
            }elseif ($msgType == ''){
                
            }
           // reply($xml);

        }else{
            exit();
        }
//        $data = json_encode($_REQUEST);
//        echo  $_GET["echostr"];
//        $data .= json_encode($GLOBALS["HTTP_RAW_POST_DATA"]);
//        open($data);
       // echo 'success';
   }
}