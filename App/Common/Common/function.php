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
        return $data['access_token'];
    }
    $url = C('TOKEN_URL').'?grant_type=client_credential&appid='.C('APPID').'&secret='.C('APPSECRET');
    $res = file_get_contents($url);
    $data = json_decode($res,true);
    S('access_token',$data,7150);
    return $data['access_token'];
}
//回复普通消息模板
    function msgText(){
        return "<xml>  
  
            <ToUserName><![CDATA[%s]]></ToUserName>  
  
            <FromUserName><![CDATA[%s]]></FromUserName>  
  
            <CreateTime>%s</CreateTime>  
  
            <MsgType><![CDATA[%s]]></MsgType>  
  
            <Content><![CDATA[%s]]></Content>  
  
            </xml>";
}
//异步通知
function sendMessage($openid){
    $url = 'http://wechat.dulishuo.com';
  //  $url = 'http://127.0.0.1/';
   echo $url .= U('Index/sendMessage');
    $data["channel"]=1;
    $data["mobile"]=2;
    $data["gateway"]=3;
    $data["isp"]=4;
    $data["linkid"]=5;
    $data["msg"]=6;

    $post = http_build_query($data);
    $len = strlen($post);
//发送
    $host = "http://wechat.dulishuo.com";
    $path = U('Index/sendMessage');
    $fp = fsockopen( $host , 80, $errno, $errstr, 30);
    if (!$fp) {
        echo "$errstr ($errno)\n";
    } else {

        $out = "POST $path HTTP/1.1\r\n";
        $out .= "Host: $host\r\n";
        $out .= "Content-type: application/x-www-form-urlencoded\r\n";
        $out .= "Connection: Close\r\n";
        $out .= "Content-Length: $len\r\n";
        $out .= "\r\n";
        $out .= $post . "\r\n";
        // echo($out);
        fwrite($fp, $out);

        //实现异步把下面去掉
        // $receive = '';
        // while (!feof($fp)) {
        // $receive .= fgets($fp, 128);
        // }
        // echo "<br />".$receive;
        //实现异步把上面去掉

        fclose($fp);
    }
}
//获取用户信息
function getUser($openid){
    //?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN
    $url = C('USER_INFO');
    $url .= '?access_token='.access_token().'&openid='.$openid .'&lang=zh_CN';
    $result = file_get_contents($url);
    $array = json_decode($result,true);
    if(!isset($array['errcode'])){
        return $array;
    }else{
        return false;
    }
}