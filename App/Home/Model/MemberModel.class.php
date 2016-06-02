<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/9
 * Time: 16:08
 */
namespace Home\Model;
use Think\Model;
class MemberModel extends Model
{

    public function add_User($user){ //添加或更新用户信息
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
        if(isset($user['id'])){
           $this->where("id={$user['id']}")->save($data);
            return $user['id'];
        }else{
            $data['at_time'] = time();
            return $this->add($data);
        }
    }

    public function insert($data){
        return $this->add($data);
    }
    public function getUser($where){
        return $this->where($where)->find();
    }
    public function getUpdate($where,$data){
        return $this->where($where)->save($data);
    }
    public function getInfo($where){
        return $this->where($where)->find();
    }
}