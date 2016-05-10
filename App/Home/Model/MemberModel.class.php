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

    public function insert($data){
        return $this->add($data);
    }

}