<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/28
 * Time: 15:19
 */

namespace Home\Controller;


use Think\Controller;

class EventController extends Controller
{
    public $time;// = time();
    public $fromUsername;
    public $toUsername;
    public $keyword;
    public $str;
    public $activity;
    public $EventKey;
    public $Event;
    public function init($obj){
        $this->time            = time();
        $this->msgXml          = msgText(); //普通消息
        $this->fromUsername   = $obj->FromUserName;  //发送信息用户openid
        $this->toUsername     = $obj->ToUserName;//开发者账号
        $this->str             = trim($obj->Content);//信息内容
        $this->msgImg          = msgImg();
        $this->Event           = $obj->Event;
        $this->EventKey        = $obj->EventKey;
    }
    //事件处理方法
    public function handle(){
        $fun = (string)$this->Event;
        return $this->$fun();
    }
    //用户已关注扫描时间
    public function scan(){



    }
    //用户未关注扫描时间
    public function subscribe(){
        $this->arr = explode('qrscene_',$this->EventKey);
        $this->EventKey = $this->arr[1];
        $shar = D('share');
        $supp = $shar->getInfo('id=' . $this->EventKey); //活动支持信息
        if (!$supp) {
            die('success');
        }
        $aid = $supp['a_id'];
        $user_id = $supp['user_id'];
        //获取扫码用户的信息
        $info = D('member')->getInfo("openid='{$this->fromUsername}'");
        $a_user_id = isset($info['id']) ? $info['id'] : 0;
        if ($user_id == $a_user_id) {
            die('success');
        }
        $share_array = $shar->getInfo("user_id = {$a_user_id} and a_id = {$aid}"); //活动信息
        if(!$share_array){
            $resu = D('activity')->getFind('id = '.$aid);
            if(isset($resu['title'])){
                $resultStr = sprintf($this->textTpl, $this->fromUsername, $this->toUsername, $this->time, 'text', $resu['title']); //推送活动信息
                echo $resultStr;
            }
        }
    }
    //用户取消关注事件
    public function unsubscribe(){
        return 'success';
    }
    //点击菜单事件处理
    public function click(){
        if($this->EventKey != 'weilaiyingyu'){
            //ScBINfXZiha6z2o4pk58hVrXJHgSCwP0dVPufaff1jg
            $class = A('Service');
            var_dump($class->sendMessage('ScBINfXZiha6z2o4pk58hVrXJHgSCwP0dVPufaff1jg',$this->fromUsername,'image'));
        }else{
            return 'success';
        }
    }
    //上报地理位置事件
    public function locaiion(){
        return 'success';
    }



}