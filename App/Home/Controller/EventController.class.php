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
        if($this->EventKey <= 0) die('success');
        $shar = D('share');
        $supp = $shar->getInfo('id=' . $this->EventKey); //活动支持信息
        if (!$supp) {
            die('success');
        }
        $aid = $supp['a_id'];
        $user_id = $supp['user_id']; //被支持者
        $userModel = D('member');
        //获取扫码用户的信息
        $user = getUser($this->fromUsername);
        $s_user_id = $userModel->addUser($user);  //支持者用户id
        $s_user_info = $userModel->getInfo('id = '.$s_user_id);
        if($user_id == $s_user_id){
            die('success');
        }

        //判断用户是否支持过别人
        $result = $shar->getInfo("a_id = {$aid} and s_user_id = {$s_user_id}");
        $model = D('Activity');
        $activity = $model->getInfo("id={$aid}");
        if(!$activity) die('success');
        if($result){  //针对此次活动用户支持过别的用户，不能重复支持
            $resultStr = sprintf($this->textTpl, $this->fromUsername, $this->toUsername, $this->time, 'text', $activity['re_invite_content']); //推送活动信息
            echo $resultStr;
            return ;
        }else{ //用户未支持过
            $s_data = array(
                'user_id' => $user_id,
                'a_id'      =>  $aid,
                's_user_id' => $s_user_id,
                'at_time'   =>  time()
            );
            D('support')->Insert($s_data); //保存支持信息
            $shar->where(array('user_id'=>$s_user_id,'a_id'=>$aid))->setInc('number'); //活动信息支持人数加1
            $number = $activity['number']+1;  //人数
            //成功邀请成员加入 推送模板消息
            tempMessage($s_user_info['openid'],$activity['invite_url'],$activity['invite_content'],$s_user_info['nickname'],'总人气值'.$number.'！');
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
    public function addActivity($array){

    }



}