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
//    $data = S('access_token');
//
//    if($data){
//        return $data['access_token'];
//    }
    $url = "http://pm.dulishuo.com/Wechat/access_token?sign=xksdsidiosdoisdasd";
  // $url = C('TOKEN_URL').'?grant_type=client_credential&appid='.C('APPID').'&secret='.C('APPSECRET');
    $res = file_get_contents($url);
    $data = json_decode($res,true);
    open($res);
    if(isset($data['access_token'])){
      //  S('access_token',$data,7150);
        return $data['access_token'];
    }
  //  S('access_token',null);
    return $data;

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
function _curl($openid,$id) {
    $url = 'http://wechat.dulishuo.com';
    $url .=  U('Index/sendMessage',array('openid'=>$openid,'id'=>$id));
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
        S('access_token',null);
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
    $image->open(getcwd().$tplImg);

    $head = new \Think\Image();

    $head->open(getcwd().'/'.$headImg);

    $image->water(getcwd().'/'.$headImg,\Think\Image::IMAGE_WATER_MARGIN,100,C('IMG_height')); //水印用户头像
    $height = $head->height()+C('IMG_height')+C('IMG_NAME_HEIGHT'); // 字符串据上的距离

    $image->text($str,getcwd().'/img/tf/msyh.ttf','20',C('IMG_TEXT_COLOR'),\Think\Image::IMAGE_WATER_MARGIN,0,0,$height);//水印用户昵称
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
    //$post=json_encode($array);
    $post=urldecode($array);
    $posturl="https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".access_token();
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,$posturl);//url
    curl_setopt($ch,CURLOPT_POST,1);//POST
    curl_setopt($ch,CURLOPT_POSTFIELDS,$post);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $result = curl_exec($ch);
    open($result);
    curl_close($ch);
    return json_encode($result);
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
 * 发送客服消息
 */
function serviceMsg($openid,$msg,$type='text'){
    if($type == 'text'){
        $msgArray = '{
                    "touser":"'.$openid.'",
                    "msgtype":"text",
                    "text":
                    {
                         "content":"'.$msg.'"
                    }
                }';
    }else if($type == 'image'){
        $msgArray = '{
                    "touser":"'.$openid.'",
                    "msgtype":"image",
                    "image":
                    {
                         "media_id":"'.$msg.'"
                    }
                }';
    }
    return sendMessage($msgArray);

}
//发送模板消息
function tempMessage($openid,$url,$message,$nickname,$number=''){
    $message_array = explode('\r\n',$message);
    if(isset($message[0]) && isset($message[1])){
        $first = $message_array[0];
        $remark = $message_array[1];
    }else{
        $first = $message_array[0];
        $remark = '--';
    }
    $first .= $number;
    $msgArray = ' {
           "touser":"'.$openid.'",
           "template_id":"'.C('template_send_id').'",
           "url":"'.$url.'",            
           "data":{
                   "first": {
                       "value":"'.$first.'",
                       "color":"#173177"
                   },
                   "keyword1":{
                       "value":"'.$nickname.'",
                       "color":"#173177"
                   },
                   "keyword2": {
                       "value":"'.date("Y-m-d H:i:s").'",
                       "color":"#173177"
                   },
                   "remark":{
                       "value":"'.$remark.'",
                       "color":"#173177"
                   }
           }
       }';
    $template_url = C('template').'?access_token='.access_token();
    return httpPost($template_url,$msgArray);
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
/*
 * 自动回复
 */
function autoMessage($keword){
    if(!$keword) return;
    $array = array(
        [
            'text'=>'软件',
            'message'   => "请分享图文<a href='http://mp.weixin.qq.com/s?__biz=MzIwMDIwMzQ5Mg==&mid=503501220&idx=1&sn=ce694b2bdad473aeb2fe80b5f46bcc58#rd'>【你们要的图书馆】史上最受欢迎图书集合，这才是五一的主线任务 </a> 至50人以上大学生微信群并截图（不要发在独立说的群里哦），然后把截图发给小助手，我们会在1天之内回复你哒，不要着急哦~
ps：不要把链接重复发在一个群里，那会引起大家的反感。请更多的分享给不知道这个福利的同学哈！",
        ],
        [
            'text'      => '雅思',
            'message'  => '请分享图文 <a href="http://mp.weixin.qq.com/s?__biz=MzIwMDIwMzQ5Mg==&mid=401172225&idx=1&sn=4344b98446bcb9c975a78834b10c804a#rd"> 【你们要的雅思】500条雅思必备资料，用它拿下7.5</a>至50人以上大学生微信群并截图（不要发在独立说的群里哦），然后把截图发给小助手，我们会在1天之内回复你哒，不要着急哦~ps：不要把链接重复发在一个群里，那会引起大家的反感。请更多的分享给不知道这个福利的同学哈！'
        ],
        [
            'text'      => '外语',
            'message'  => '请分享图文<a href="http://mp.weixin.qq.com/s?__biz=MzIwMDIwMzQ5Mg==&mid=401171949&idx=1&sn=b86939bb0fdf194da673c673d2995d7e#rd" >【你们要的小语种】学完这些语言，你就可以去火星救援</a>至50人以上大学生微信群并截图（不要发在独立说的群里哦），然后把截图发给小助手，我们会在1天之内回复你哒，不要着急哦~
ps：不要把链接重复发在一个群里，那会引起大家的反感。请更多的分享给不知道这个福利的同学哈！'
        ],
        [
            'text'      => '文书',
            'message'  => '请分享图文<a href="http://mp.weixin.qq.com/s?__biz=MzIwMDIwMzQ5Mg==&mid=401067182&idx=1&sn=cc169b646b18f16b567a9ccca224b66e#rd" > 【你们要的文书】史上最强の文书礼包，再写不好就狗带</a>至50人以上大学生微信群并截图（不要发在独立说的群里哦），然后把截图发给小助手，我们会在1天之内回复你哒，不要着急哦~
ps：不要把链接重复发在一个群里，那会引起大家的反感。请更多的分享给不知道这个福利的同学哈！'
        ],
        [
            'text'      => '网站',
            'message'  => '请分享图文<a href="http://mp.weixin.qq.com/s?__biz=MzIwMDIwMzQ5Mg==&mid=400686925&idx=1&sn=0028109165e58fd8a4cf0614ef125e1b#rd"> 【你们要的神器】有了这八大网站，人生瞬间进入Easy模式！</a>至50人以上大学生微信群并截图（不要发在独立说的群里哦），然后把截图发给小助手，我们会在1天之内回复你哒，不要着急哦~
ps：不要把链接重复发在一个群里，那会引起大家的反感。请更多的分享给不知道这个福利的同学哈！'
        ],
        [
            'text'      => 'gmat',
            'message'  => '请分享图文<a href="http://mp.weixin.qq.com/s?__biz=MzIwMDIwMzQ5Mg==&mid=400478683&idx=1&sn=d624900ad476a5d894b3ab5aee6a583e#rd"> 【你们要的GMAT】500条GMAT必备资料，用它拿下700+</a>至50人以上大学生微信群并截图（不要发在独立说的群里哦），然后把截图发给小助手，我们会在1天之内回复你哒，不要着急哦~
ps：不要把链接重复发在一个群里，那会引起大家的反感。请更多的分享给不知道这个福利的同学哈！'
        ],
        [
            'text'      => '托福',
            'message'  => '请分享图文 <a href="http://mp.weixin.qq.com/s?__biz=MzIwMDIwMzQ5Mg==&mid=400141498&idx=1&sn=a7141a5b09eebd123891d8288cf0b60f#rd"> 【你们要的托福】700条托福必备资料，用它拿下110 </a>至50人以上大学生微信群并截图（不要发在独立说的群里哦），然后把截图发给小助手，我们会在1天之内回复你哒，不要着急哦~
ps：不要把链接重复发在一个群里，那会引起大家的反感。请更多的分享给不知道这个福利的同学哈！'
        ],
        [
            'text'      => '简历',
            'message'  => '请分享图文 <a href="http://mp.weixin.qq.com/s?__biz=MzIwMDIwMzQ5Mg==&mid=401394249&idx=1&sn=23d85904cf69ddffd7093f39d6ad22b0#rd"> 【你们要的简历】你离世界五百强，只差这份简历</a>至50人以上大学生微信群并截图（不要发在独立说的群里哦），然后把截图发给小助手，我们会在1天之内回复你哒，不要着急哦~
ps：不要把链接重复发在一个群里，那会引起大家的反感。请更多的分享给不知道这个福利的同学哈！'
        ],
        [
            'text'      => 'GRE',
            'message'  => '请分享图文<a href="http://mp.weixin.qq.com/s?__biz=MzIwMDIwMzQ5Mg==&mid=208845117&idx=1&sn=4efef7ea2451bbd49ff903bb2dd5532c#rd"> 客官你们点的干货上菜了！GRE大礼包趁热吃！</a> 至50人以上大学生微信群并截图（不要发在独立说的群里哦），然后把截图发给小助手，我们会在1天之内回复你哒，不要着急哦~ps：不要把链接重复发在一个群里，那会引起大家的反感。请更多的分享给不知道这个福利的同学哈！'
        ],
        [
            'text'      => '技能',
            'message'  => '请分享图文 <a href="http://mp.weixin.qq.com/s?__biz=MzIwMDIwMzQ5Mg==&mid=402326289&idx=1&sn=e90ac25e63049dc8f2e2ae6026fb9275#rd"> 【你们要的新年礼物】帮你实习加薪拿奖学金的五大技能</a>至50人以上大学生微信群并截图（不要发在独立说的群里哦），然后把截图发给小助手，我们会在1天之内回复你哒，不要着急哦~
ps：不要把链接重复发在一个群里，那会引起大家的反感。请更多的分享给不知道这个福利的同学哈！'
        ],
        [
            'text'      => '读书',
            'message'  => '请分享图文<a href="http://mp.weixin.qq.com/s?__biz=MzIwMDIwMzQ5Mg==&mid=503501220&idx=1&sn=ce694b2bdad473aeb2fe80b5f46bcc58#rd"> 【你们要的图书馆】史上最受欢迎图书集合，这才是五一的主线任务</a>至50人以上大学生微信群并截图（不要发在独立说的群里哦），然后把截图发给小助手，我们会在1天之内回复你哒，不要着急哦~
ps：不要把链接重复发在一个群里，那会引起大家的反感。请更多的分享给不知道这个福利的同学哈！'
        ],
        [
            'text'      => '四六级',
            'message'  => '请分享图文 <a href="http://dwz.cn/3hNpGs"> 【懒人计划第一季】最牛备考资料，2小时搞定你的四六级！</a>
至朋友圈或50人以上大学生微信群并截图（不要发在独立说的群里哦）
然后把截图发给小助手，我们会在1天之内回复你哒，不要着急哦~ 
ps：不要把链接重复发在一个群里，那会引起大家的反感。请更多分享给不知道这个福利的同学哈！
要发给小助手哦，发给后台没有用的'
        ],
        [
            'text'      => '漫威',
            'message'  => '获得漫威电影大礼包的步骤：

1、请转发文章 http://dwz.cn/3olyFH 到朋友圈或100人以上微信群。

2、把转发截图发给下方任意一个小助手，不要发到独立说微信群里哦。

3、要发给小助手个人，不是发到公众号哦 ，网盘资源链接就会发给你啦

对啦，如果有时间的话。帮小搜君做个调查问卷哈：http://form.mikecrm.com/Zsi9rb#rd'
        ],
        [
            'text'      => '9',
            'message'  => '1、请转发文章http://dwz.cn/3rY5hk到朋友圈或100人以上微信群，附带一句你的进群理由，比如“这个活动不错￼”

2、把转发截图发给下方任意一个小助手，不要把链接重复发在一个群里哦，那会引起大家的反感￼。
（也不要发到独立说微信群里哦）

3、要发给小助手个人，不是发到公众号哦 ￼'
        ],
    );
    $message = '';
    for($i=0;$i<count($array);$i++){
        if($array[$i]['text'] == $keword){
            $message =  $array[$i]['message'];
        }
    }
    return $message;
}