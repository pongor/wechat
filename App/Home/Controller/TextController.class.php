<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/27
 * Time: 17:07
 */

namespace Home\Controller;


use Think\Controller;
/*
 * 用户发送文字处理
 */
class TextController //extends Controller
{
    public $time;// = time();
    public $fromUsername;
    public $toUsername;
    public $keyword;
    public $str;
    public $activity;
    public function __construct()
    {
        $this->time = time();
        $this->msgXml = msgText(); //普通消息
     //   $this->msgImg = msgImg();
    }

    //搜索文字
    public function search(){
        if(!$this->str) return false;
        $model = D('activity');
        //活动检索
        $where = "back_keyword = '{$this->str}' and start_time < {$this->time} and end_time > {$this->time}";
        $res = $model->getFind($where);
        if(!$res) return false;
        return $res;
    }
    //信息处理方法
    public function handle(){
        $class = A('Service'); //调用客服消息类
        $this->activity = $this->search();
        if($this->activity){
            if($this->activity['is_start'] != 1){
                $resultStr = sprintf($this->msgXml, $this->fromUsername, $this->toUsername, $this->time, 'text', '这个活动已经结束报名啦，下次早点来哦！'); //推送活动结束信息
                echo $resultStr;
                die;
            }
            //被动回复
            $resultStr = sprintf($this->msgXml, $this->fromUsername, $this->toUsername, $this->time, 'text', $this->activity['title']); //推送活动信息
            echo $resultStr;
            $this->activityInfo($class);
            return ;
        }
        //非活动信息
        $this->msg = $this->keywords();
        $resultStr = sprintf($this->msgXml, $this->fromUsername, $this->toUsername, $this->time, 'text', $this->msg); //推送活动信息
        echo $resultStr;
        if($this->str == 9){
            $media_id = 'aP7svrLdd53I6tixB0BOYKbEpa76kEk26asXz0gmmL8';
        }else{
            $media_id = 'aP7svrLdd53I6tixB0BOYJIqS_Oa3TXAHk-XFCoxJ7U';
        }
        var_dump($class);
        $class->sendMessage($media_id,$this->fromUsername);
        return;
    }
    //活动信息的其他规则
    public function activityInfo($obj){
        $user = getUser($this->fromUsername); // 获取用户信息
        $model = D('member');
        $result = $model->getUser(array('openid'=>$this->fromUsername));

        $data = [
            'nickname'      =>  $user['nickname'],
            'headimgurl'    =>  $user['headimgurl'],
            'openid'        =>  $user['openid'],
            'sex'           =>  $user['sex'],
            'province'      =>  $user['province'],
            'city'          =>   $user['city'],
            'country'       =>  $user['country'],
            'subscribe_time' => $user['subscribe_time'],
            'privilege'     =>  $user['privilege'],
            'remark'        =>   $user['remark'],
        ];
        if($result){ //如果用户存在
            $model->getUpdate('id='.$result['id'],$data);
            $user_id = $result['id'];

        }else {
            $data['at_time'] = time();
            $user_id = $model->insert($data);  //是新用户.
        }
        $share = D('share');
        $share_info = $share->getInfo('user_id='.$user_id.' and a_id='.$this->activity['id']);

        //拿到分享图片
        if($share_info && ($share_info['up_time'] + 3*24*60*60) > time() && $share_info['share']){  //素材未过期
            $media_id = $share_info['media_id'];
        }else if(($share_info['up_time'] + 3*24*60*60) <= time() && $share_info['share']){  //素材过期  重新上传
            $media_id = add_material(array('filename'=>__APP__.ltrim($share_info['share'],'.'), 'content-type'=>'image/png','filelength'=>'11011')); //上传素材
            $share->getUpdate('id='.$share_info['id'],array('media_id'=>$media_id,'up_time'=>time())); //更新用户活动数据
        }else{  //不存在信息
            //保存用户分享信息
            if(!$share_info){
                $share_data = array(
                    'user_id' => $user_id,
                    'a_id'      =>  $this->activity['id'],
                    'share'     =>  '',
                    'up_time'   =>  time(),
                    'at_time'   =>  time(),
                    'media_id'  =>  '',
                    'number'    =>  0
                );
                $share_id =  $share->Insert($share_data);
            }else{
                $share_id = $share_info['id'];
            }

            //生成二维码图片ca
            $array = array(
                'action_info' => array(
                    'scene' => array(
                        'scene_id' => $share_id
                    ),
                ),
            );
            $codeUrl = getCode($array);

            $file_code = codeImg($codeUrl,$user_id);//saveCode($codeUrl, 100); // 二维码图片路径

            //下载用户头像
            $headimg = downloadFile($data['headimgurl'].'.jpg');

            //生成分享图片
            $headimg = get_lt_rounder_corner($headimg, $data['openid']); //圆角头像

            $fiel =  imgTo($this->activity['back_pic'],$headimg,$file_code,$data['nickname']);

            $fiel =  ltrim($fiel,'.');
            //上传微信素材服务器  获取素材media_id
            $file_data = array(
                'filename'=>__APP__.$fiel,  //国片相对于网站根目录的路径
                'content-type'=>'image/png',  //文件类型
                'filelength'=>'11011'         //图文大小
            );
            $media_id = add_material($file_data);

            //保存分享的图片 与微信上传的素材
            $share_save = array(
                'share' => $fiel,
                'media_id' => $media_id
            );
            $share->getUpdate(array('id'=>$share_id),$share_save);
        }
        sleep(1);
        if($this->activity['text_content']){
            $array = explode('||',$this->activity['text_content']);
            //发送用户参加活动的信息
            for($i=0;$i<count($array);$i++){
                if(isset($array[$i]) && $array[$i]){
                    $obj->sendMessage($array[$i],$this->fromUsername);
                }
            }
        }
        $obj->sendMessage($media_id,$this->fromUsername);
    }
    //关键字自动回复
    public function keywords(){
        return autoMessage($this->str);
    }

}