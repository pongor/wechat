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
             //   sendMessage($toUsername);
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
    public function sendMessage(){
        $openid = $_POST['openid'];
        $token = access_token();
        for($i=0;$i<4;$i++)
        {
            $contentStr="这是发送内容".$i;
            $contentStr=urlencode($contentStr);
            $a=array("content"=>"{$contentStr}");
            $b=array("touser"=>"{$openid}","msgtype"=>"text","text"=>$a);
            $post=json_encode($b);
            $post=urldecode($post);
            $posturl="https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$token;
            $ch=curl_init();
            curl_setopt($ch,CURLOPT_URL,$posturl);//url
            curl_setopt($ch,CURLOPT_POST,1);//POST
            curl_setopt($ch,CURLOPT_POSTFIELDS,$post);
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
           $res =  curl_exec($ch);

            curl_close($ch);
            open(json_encode($res));
        }
    }
}