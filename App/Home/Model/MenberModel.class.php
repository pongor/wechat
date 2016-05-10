<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/9
 * Time: 16:08
 */
namespace Think;
use Think\Model;
class MenberModel extends Model
{
    public function insert($data){
        return $this->add($data);
    }

}