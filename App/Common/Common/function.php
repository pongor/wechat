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
    $url .= U('Index/sendMessage');
   // file_get_contents($url);
    $param = array(
        'openid'=>$openid,
    );
    $urlinfo = parse_url($url);

    $host = $urlinfo['host'];
    $path = $urlinfo['path'];
    $query = isset($param)? http_build_query($param) : '';
    $port = 80;
    $errno = 0;
    $errstr = '';
    $timeout = 10;

    $fp = fsockopen($host, $port, $errno, $errstr, $timeout);

    $out = "POST ".$path." HTTP/1.1\r\n";
    $out .= "host:".$host."\r\n";
    $out .= "content-length:".strlen($query)."\r\n";
    $out .= "content-type:application/x-www-form-urlencoded\r\n";
    $out .= "connection:close\r\n\r\n";
    $out .= $query;
    fputs($fp, $out);
    fclose($fp);
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