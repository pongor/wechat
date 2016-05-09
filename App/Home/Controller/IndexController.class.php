<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {

    public function index(){
        open(json_encode($_REQUEST).'-----'.json_encode($GLOBALS["HTTP_RAW_POST_DATA"]));
        if(checkSignature()){
            echo $_GET['echostr'];
            $xml = $GLOBALS["HTTP_RAW_POST_DATA"];
            $postObj = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;

            $toUsername = $postObj->ToUserName;

            $keyword = trim($postObj->Content);
            $msgType = $postObj->MsgType;
            $time = time();
            if($msgType == 'text'){
                if($keyword == 'Hello2BizUser'){
                    $textTpl = msgText();
                    $contentStr = '你是第一次关注';
                }else{
                    $textTpl = msgText();
                    $contentStr = '找死是么。';
                }
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
                die;
            }elseif ($msgType == 'image'){
                $picUrl = $postObj->PicUrl;
                $MediaId = $postObj->MediaId;

            }
           // reply($xml);

        }else{
            exit();
        }

   }
}