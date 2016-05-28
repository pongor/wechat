<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/27
 * Time: 16:03
 */

namespace Home\Controller;


use Think\Controller;
//微信接口入口文件
class WechatController extends Controller
{

    public function index(){
        if(!checkSignature()){
            die();
        }
        echo $_GET['echostr'];
        $xml = $GLOBALS["HTTP_RAW_POST_DATA"];
        $postObj = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        self::factory($postObj);
    }
    public static function factory($obj){
        if(!is_object($obj)){
            return 'success';
        }
        $class = A(ucfirst($obj->MsgType));
        $class->fromUsername = $obj->FromUserName;
        $class->toUsername = $obj->ToUserName;
        $class->str = trim($obj->Content);
        $class->handle();
    }
    public function test(){
        $json = '{
    "ToUserName":"gh_cbfe978fe9e3",
    "FromUserName":"o5Dq_vnlLTWQuqQ4taaVVjYqir0A",
    "CreateTime":"1463645435",
    "MsgType":"text",
    "Content":"9",
    "MsgId":"6286309276681876036"
}';
        $obj = json_decode($json);

        self::factory($obj);

    }

}