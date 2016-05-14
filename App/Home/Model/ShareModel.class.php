<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/14
 * Time: 15:50
 */

namespace Home\Model;


use Think\Model;

class ShareModel extends Model
{
    protected $tableName  = 'member_activity';
    /*
     * 获取用户参加活动信息
     */
    public function getInfo($where){
        return $this->where($where)->find();
    }
    /*
     * 保存用户参加活动的信息
     */
    public function Insert($data){
        return $this->add($data);
    }
    /*
     * 更新用户活动信息
     */
    public function getUpdate($where,$data){
        return $this->where($where)->save($data);
    }
}