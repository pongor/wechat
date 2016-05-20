<?php
/**
 * Created by PhpStorm.
 * User: yan
 * Date: 2016/3/30
 * Time: 11:33
 */

namespace Backend\Model;


use Think\Model;

class MemberactivityModel extends Model {
    protected $tableName = 'member_activity'; 
	//获取列表
    public function getList($where=array(),$order = ''){
    	$start = $where['start'] ? $where['start'] : 0;
        unset($where['start']);
        return $this->where($where)->limit($start,20)->order($order)->select();
         var_dump($this->getLastSql());die;
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