<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/27
 * Time: 17:30
 */

namespace Home\Controller;


use Think\Controller;
//客服信息处理
class ServiceController extends Controller
{
    public $time;
    public function __construct()
    {
        $this->time = time();
    }

    /*
     * @param content 发送的消息内容或 media_id
     * @param $type 发送消息类型  text 文本  image 图片  template 模板消息  --- 待实现news 文本消息
     */
    public function sendMessage($content,$openid,$type='text'){
        if(!$type) return false;
        $msg = $this->$type($content,$openid);
        $result = sendMessage($msg);
        return isset($result['errcode']) && $result['errcode'] == 0;
    }
    //文本消息数据包
    public function text($content,$openid){
        $msgArray = '{
                    "touser":"'.$openid.'",
                    "msgtype":"text",
                    "text":
                    {
                         "content":"'.$content.'"
                    }
                }';
        return $msgArray;
    }
    //图片消息数据包
    public function image($content,$openid){
        $array = '{
                    "touser":"'.$openid.'",
                    "msgtype":"image",
                    "image":
                    {
                         "media_id":"'.$content.'"
                    }
                }';
        return $array;
    }
    //上传永久素材
    public function upload($path = ''){
        $file_info = array(
            'filename'=>__APP__.'/img/caidan.jpg',  //国片相对于网站根目录的路径
            'content-type'=>'image/jpg',  //文件类型
            'filelength'=>'11011'         //图文大小
        );

        $access_token=access_token();
       $url="https://api.weixin.qq.com/cgi-bin/material/add_material?access_token={$access_token}&type=image";
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
        var_dump($result);die;
        curl_close ( $ch1 );
        if(curl_errno()==0){
            $result=json_decode($result,true);

            return $result['media_id'];
        }else {
            return false;
        }
    }

}