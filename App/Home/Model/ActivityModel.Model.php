<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/14
 * Time: 11:51
 */

namespace Home\Model;


use Think\Model;

class ActivityModel extends Model
{
    public function getFind($where){
        return $this->where($where)->find();
    }

}