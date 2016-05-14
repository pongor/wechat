<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/14
 * Time: 16:23
 */

namespace Home\Model;


use Think\Model;

class SupportModel extends Model
{
    /*
     * 保存支持着数据
     */
    public function Insert($data){
        return $this->add($data);
    }
    /*
     * 获取支持着信息
     */
    public function getInfo($where){
        return $this->where($where)->find();
    }

}