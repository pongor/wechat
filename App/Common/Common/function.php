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
    curl_setopt ( $ch1, CURLOPT_SAFE_UPLOAD, false); //关键
    $timeout = 5;
   $real_path="{$_SERVER['DOCUMENT_ROOT']}{$file_info['filename']}";
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
        return $result['media_id'];
    }else {
        return false;
    }
}
//异步通知
function _curl($openid) {
    $url = 'http://wechat.dulishuo.com';
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
/*
 * @param tplImg  模板图片路径，
 * @param  headImg 用户头像相对路径
 * @param  codeImg 用户活动二维码 路径
 * @param  str  用户昵称
 */
function imgTo($tplImg,$headImg,$codeImg,$str='pongor'){


    $image = new \Think\Image();
    echo getcwd().$tplImg;
    var_dump($image->open(getcwd().$tplImg));

    $head = new \Think\Image();

    $head->open(getcwd().'/'.$headImg);

    $image->water(getcwd().'/'.$headImg,\Think\Image::IMAGE_WATER_MARGIN,100,C('IMG_height')); //水印用户头像
    $height = $head->height()+C('IMG_height')+C('IMG_NAME_HEIGHT'); // 字符串据上的距离

    $image->text($str,getcwd().'/img/ttf/msyh.ttf','20',C('IMG_TEXT_COLOR'),\Think\Image::IMAGE_WATER_MARGIN,0,0,$height);//水印用户昵称
    $code = new Think\Image();

    $code->open(getcwd().'/'.$codeImg);
    $codeThumb = './img/temp/';
    mkDirs($codeThumb);
    $codeThumb .= time().rand(rand(100,999),3000).'.jpg';
    $code->thumb(C('IMG_CODE'),C('IMG_CODE'))->save($codeThumb);

    $image->water(getcwd().'/'.$codeThumb,\Think\Image::IMAGE_WATER_CODE ,100,C('IMG_LEFT'),C('IMG_NEXT')); //水印二维码
    $file = './img/user/f/';
    mkDirs($file);
    $file .= time().'-'.rand(0,10000).'.png';
    $image->save($file);
   return $file;

}
//将用户头像生成圆角图片
function get_lt_rounder_corner($file_path,$openid) {
    $image_file = getcwd().'/'.$file_path;
    $thumb = new \Think\Image();
    $thumb->open($image_file);
    $thumbTemp = './img/temp/';
    mkDirs($thumbTemp);
    $thumbTemp .= time().'-'.rand(1,10000).'.jpg';
    $thumb->thumb(C('IMG_HEADER_SIZE'),C('IMG_HEADER_SIZE'))->save($thumbTemp); //缩放头像
    $image = new Imagick(getcwd().'/'.$thumbTemp);
//$image->newPseudoImage(200, 200, "magick:rose");
    $image->setImageFormat("png");
    $image->roundCorners(C('IMG_HEADER_SIZE'),C('IMG_HEADER_SIZE')/2);
    $path = '/img/'.$openid.'.png';
    if($image->writeImage(getcwd().$path)){
        return $path;
    }else{
        return false;
    }
}
//发送客服消息 array 传入消息信息
function sendMessage($array){
    $post=json_encode($array);
    $post=urldecode($post);
    $posturl="https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".access_token();
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,$posturl);//url
    curl_setopt($ch,CURLOPT_POST,1);//POST
    curl_setopt($ch,CURLOPT_POSTFIELDS,$post);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
    curl_exec($ch);
    curl_close($ch);
}
//将用户头像保存到本地

function downloadFile($url,$savePath='./img')
{
    $curl = curl_init($url);
    $filename = $savePath.date("Ymdhis").".jpg";
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
    $imageData = curl_exec($curl);
    curl_close($curl);
    $tp = @fopen($filename, 'a');
    fwrite($tp, $imageData);
    fclose($tp);
    return $filename;
    die;
//    $fileName = rand(0,1000).'.jpg';
//    $file = file_get_contents($url);
//    file_put_contents($savePath.'/'.$fileName,$file);
//    return $fileName;
}
//创建二维码tichet  并返回获取二维码图片的url
function getCode($action_info=array(),$expire_seconds=259200,$action_name='QR_SCENE'){
    $action_info['expire_seconds'] = $expire_seconds; // 有效期
    $action_info['action_name'] = $action_name; // 临时 ticket
     $ticket_url = C('CODE_ticket').'?access_token='.access_token();
    $post = json_encode($action_info);
    $result = httpPost($ticket_url,$post);
    $array = json_decode($result,true);

    if(isset($array['errcode'])){

        S('access_token',null);
    }
    return $array['url'];
    return  C('CODE_IMG')."?ticket=".urlencode($array['ticket']);

}
// 生成二维码
function codeImg($data,$user_id = 1){
    vendor("phpqrcode.phpqrcode");
    // 纠错级别：L、M、Q、H
    $level = 'L';
    // 点的大小：1到10,用于手机端4就可以了
    $size = 6;
    // 下面注释了把二维码图片保存到本地的代码,如果要保存图片,用$fileName替换第二个参数false
    $path = "./img/";
    // 生成的文件名
     $fileName = $path.$user_id.'.png';
    QRcode::png($data, $fileName, $level, $size);
    return $fileName;
}
//保存微信二维码图片 二维码图片存在则覆盖
function saveCode($code_url,$user_id){
    //保存微信图片
    $ch = curl_init($code_url);
    curl_setopt($ch,CURLOPT_HEADER,0);
    curl_setopt($ch ,CURLOPT_NOBODY,0);
    curl_setopt($ch ,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch ,CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($ch ,CURLOPT_RETURNTRANSFER,1);
    $pack = curl_exec($ch);
    curl_close($ch);

    $filename ='./img/code/';
    mkDirs($filename);
    $filename .= $user_id.'.jpg';
    $local_file = fopen($filename,"w");
    if(false !== $local_file){
        if(false !== fwrite($local_file,$pack)){
            fclose($local_file);
        }
    }
    return $filename;
}

//发送post请求
function httpPost($posturl,$post){
    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_URL,$posturl);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt ($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
    $result = curl_exec ($ch);
    curl_close($ch);
    return $result;
}
function dowload($url,$filename=''){

    if($url=="") return false;

    if($filename=="") {
        $ext=strrchr($url,".");
        $filename='./img/'.date("YmdHis").$ext;
    }
    ob_start();
    readfile($url);
    $img = ob_get_contents();
    ob_end_clean();
    $size = strlen($img);
    $fp2=@fopen($filename, "w");
    fwrite($fp2,$img);
    fclose($fp2);
    return $filename;
}
/*
 * 递归创建目录
 * dir 目录
 */
function mkDirs($dir){
    if(!is_dir($dir)){
        if(!mkDirs(dirname($dir))){
            return false;
        }
        if(!mkdir($dir,0777)){
            return false;
        }
    }
    return true;
}