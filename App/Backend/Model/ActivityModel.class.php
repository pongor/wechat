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

    //自动验证
    protected $_validate = array(
        array('title','require','名称不能为空'),
        array('title','isExist','已存在该活动','3','callback')
    );

    //自动完成
    protected $_auto = array (
        array('edit_time','time','3','function'),
        array('invite_num','strToInt','3','callback'),
        array('egg_num','strToInt','3','callback'),
        array('continue_num','strToInt','3','callback'),
        array('rank_list','strToInt','3','callback'),
        array('start_time','strToTime','3','callback'),
        array('end_time','strToTime','3','callback'),
        array('notice_time','strToTime','3','callback'),
        array('success_condition','convert','3','callback'),
        array('text_content','text','3','callback'),
     );

    function isExist($title){
        $r = $this->getField(array('title'=>$title),'id');
        $id = cookie('activity_id');
        cookie('activity_id',null);
        if($r){
            if($r['id'] == $id){
                return true;
            }
            return false;
        }else{
            return true;
        }
    }

    function convert($success_condition){
       if(trim($success_condition) == 'A'){
            $success_condition = 1;
        }else if(trim($success_condition) == 'B'){
            $success_condition = 2;
        }else{
            $success_condition = 0;
        }
        return $success_condition;
    }

    //格式转换 str -> time
    function strToTime($value){
        $value = ($value != '') ?  strtotime($value) : '';
        return $value;
    }
    //格式转换str -> int
    function strToInt($str){
        return intval($str);
    }

    //将多个文本框合并成一个
    function text($text){
        $text_content = '';
        foreach ($text as $k => $v) {
            if($v != ''){
                $text_content .= $v.'||';
            }
        }
        
        if($text_content != ''){
            $text_content = substr($text_content,0,strlen($text_content)-2); 
        }
        return $text_content;
    }

	//获取列表
    public function getList($where=array()){
        $start = $where['start'] ? $where['start'] : 0;
        unset($where['start']);
        return $this->where($where)->limit($start,20)->select();

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