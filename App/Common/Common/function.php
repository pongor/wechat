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
//回复图片消息
function msgImg(){
    return "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[%s]]></MsgType>
    <Image>
    <MediaId><![CDATA[%s]]></MediaId>
    </Image>
    </xml>";
}
//上传微信素材
/*
 * $file_info=array(
    'filename'=>'/images/1.png',  //国片相对于网站根目录的路径
    'content-type'=>'image/png',  //文件类型
    'filelength'=>'11011'         //图文大小
);
 */
function add_material($file_info){
    $access_token=access_token();
    $url="https://api.weixin.qq.com/cgi-bin/media/upload?access_token={$access_token}&type=image";
    $ch1 = curl_init ();
    curl_setopt ( $ch1, CURLOPT_SAFE_UPLOAD, false);
    $timeout = 5;
   echo $real_path="{$_SERVER['DOCUMENT_ROOT']}{$file_info['filename']}";
    $data= array("media"=>"@$real_path",'form-data'=>$file_info);
    curl_setopt ( $ch1, CURLOPT_URL, $url );
    curl_setopt ( $ch1, CURLOPT_POST, 1 );
    curl_setopt ( $ch1, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt ( $ch1, CURLOPT_CONNECTTIMEOUT, $timeout );
    curl_setopt ( $ch1, CURLOPT_SSL_VERIFYPEER, FALSE );
    curl_setopt ( $ch1, CURLOPT_SSL_VERIFYHOST, false );
    curl_setopt ( $ch1, CURLOPT_POSTFIELDS, $data );
    $result = curl_exec ( $ch1 );
    curl_close ( $ch1 );
    if(curl_errno()==0){
        $result=json_decode($result,true);
        var_dump($result);
        return $result['media_id'];
    }else {
        return false;
    }
}
//异步通知
function _curl($openid) {
    $url = 'http://wechat.dulishuo.com';
  //  $url = 'http://127.0.0.1/';
    $url .=  U('Index/sendMessage',array('openid'=>$openid));
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_TIMEOUT,1);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
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
//生成图片   用户头像地址，活动id
function imgTo(){
    $image = new \Think\Image();

    $image->open('./img/2531170_213554844000_2.jpg');//->water('./img/bd_logo1.jpg',\Think\Image::IMAGE_WATER_NORTHWEST)->save(__APP__."/wechat/water.jpg");

    $image->water('./img/bd_logo3.png',\Think\Image::IMAGE_WATER_NORTH,100);
    $image->save('./a.jpg');
   // var_dump($a);

}
//生成圆角图片
function get_lt_rounder_corner($radius) {
    $img     = imagecreatetruecolor($radius, $radius);  // 创建一个正方形的图像
    $bgcolor    = imagecolorallocate($img, 255, 255, 255);   // 图像的背景
    $fgcolor    = imagecolorallocate($img, 0, 0, 0);
    imagefill($img, 0, 0, $bgcolor);
    // $radius,$radius：以图像的右下角开始画弧
    // $radius*2, $radius*2：已宽度、高度画弧
    // 180, 270：指定了角度的起始和结束点
    // fgcolor：指定颜色
    imagefilledarc($img, $radius, $radius, $radius*2, $radius*2, 180, 270, $fgcolor, IMG_ARC_PIE);
    // 将弧角图片的颜色设置为透明
    imagecolortransparent($img, $fgcolor);
    // 变换角度
    // $img = imagerotate($img, 90, 0);
    // $img = imagerotate($img, 180, 0);
    // $img = imagerotate($img, 270, 0);
    // header('Content-Type: image/png');
    // imagepng($img);
    return $img;
}