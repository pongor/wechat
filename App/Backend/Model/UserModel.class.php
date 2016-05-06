<?php
/**
 * Created by PhpStorm.
 * User: yan
 * Date: 2016/3/30
 * Time: 11:33
 */

namespace Backend\Model;


use Think\Model;

class UserModel extends Model {

	//获取列表
    public function getList($where=array()){
         return $this->where($where)->select();
        echo $this->_sql();

    }

    //获取指定列
    public function getField($where=array(),$field=''){
    	return $this->where($where)->field($field)->find();
    }


}