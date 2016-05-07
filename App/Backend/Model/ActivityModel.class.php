<?php
/**
 * Created by PhpStorm.
 * User: yan
 * Date: 2016/3/30
 * Time: 11:33
 */

namespace Backend\Model;


use Think\Model;

class ActivityModel extends Model {

	//获取列表
    public function getList($where=array()){
         return $this->where($where)->select();
        echo $this->_sql();

    }

    //查询记录条数
    public function countList1($where = array()){
        return $this->where($where)->count();
    }

    //获取指定列
    public function getField($where=array(),$field=''){
    	return $this->where($where)->field($field)->find();
    }

    //删除
    public function deleteData($where){
    	return $this->where($where)->delete();
    }

    //新建
    public function addData($data){
    	return $this->add($data);
    }

    //修改
    public function saveData($where,$data){
    	return $this->where($where)->save($data);
    }


}