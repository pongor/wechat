<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/6
 * Time: 14:43
 */
function open($txt){
    $myfile = fopen("newfile.txt", "a+") or die("Unable to open file!");
    fwrite($myfile, $txt."\r\n");
    fclose($myfile);
}
/*
 * 验证消息是否来自微信
 */
function checkSignature()
{
    $signature = I("get.signature");
    $timestamp = I("get.timestamp");
    $nonce = I("get.nonce");

    $token = C('TOKEN');
    $tmpArr = array($token, $timestamp, $nonce);
    sort($tmpArr, SORT_STRING);
    $tmpStr = implode( $tmpArr );
    $tmpStr = sha1( $tmpStr );
    if( $tmpStr == $signature ){
        return true;
    }else{
        return false;
    }
}
/*
 * 获取微信 access_TOKEN
 */
function access_token(){
    $data = S('access_token');

    if($data){
        return $data;
    }
    $url = C('TOKEN_URL').'?grant_type=client_credential&appid='.C('APPID').'&secret='.C('APPSECRET');
    $res = file_get_contents($url);
    $data = json_decode($res,true);
    S('access_token',$data,7150);
    return $data;
}
function reply($postStr){

    if (!empty($postStr)){

        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

        $fromUsername = $postObj->FromUserName;

        $toUsername = $postObj->ToUserName;

        $keyword = trim($postObj->Content);

        $time = time();

        $textTpl = "<xml>  
  
            <ToUserName><![CDATA[%s]]></ToUserName>  
  
            <FromUserName><![CDATA[%s]]></FromUserName>  
  
            <CreateTime>%s</CreateTime>  
  
            <MsgType><![CDATA[%s]]></MsgType>  
  
            <Content><![CDATA[%s]]></Content>  
  
            <FuncFlag>0<FuncFlag>  
  
            </xml>";

        if(!empty( $keyword ))

        {

            $msgType = "text";

            $contentStr = '你好啊，屌丝';

            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);

            echo $resultStr;

        }else{

            echo '咋不说哈呢';

        }

    }else {

        echo '咋不说哈呢';

        exit;

    }
}