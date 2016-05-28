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
        $msg = self::$type($content,$openid);
        $result = sendMessage($msg);
        return isset($result['errcode']) && $result['errcode'] == 0;
    }
    //文本消息数据包
    public static function text($content,$openid){
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

}