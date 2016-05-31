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
        //    die();
        }
      $xml = "
        <xml><ToUserName><![CDATA[gh_cbfe978fe9e3]]></ToUserName>
<FromUserName><![CDATA[o0W5ms1hZCcATLP8hv5lV3QHogO0]]></FromUserName>
<CreateTime>1464419014</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[click]]></Event>
<EventKey>6289631773187998165</EventKey>
</xml>";
        echo $_GET['echostr'];
      //  $xml = $GLOBALS["HTTP_RAW_POST_DATA"];
        $postObj = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        self::factory($postObj);
    }
    public static function factory($obj){
        if(!is_object($obj)){
            return false;
        }
        $class = A(ucfirst($obj->MsgType));
        if(false == $class) die('success');
        $class->init($obj);
         $class->handle();
        var_dump($class);
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