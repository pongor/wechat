<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/13
 * Time: 10:59
 * 用户参加活动的表
 */

namespace Backend\Model;


use Think\Model;

class UserModel extends Model
{
    /*
     * 获取用户活动信息
     */
    public function getInfo($where){
        return $this->where($where)->find();
    }
    /*
     * 保存用户参加活动的信息
     */
    public function getInsert($data){
        return $this->add($data);
    }
    /*
     * 更新用户参加活动的信息
     */
    public function getUpdate($where,$data){
        return $this->where($where)->save($data);
    }

}