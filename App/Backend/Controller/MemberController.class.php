<?php
namespace Backend\Controller;

use Think\Controller;
class MemberController extends RbacController{
	public function index(){
		$nickname = I('get.nickname');
		$condition = I('get.condition');
        $activity_id = intval(I('get.activity_id'));
        $p = I('get.p');
        if($nickname){
            $memberList = D('Member')->where(array('nickname'=>array('like','%'.$nickname.'%')))->select();
            $ids = array('');
            foreach($memberList as $key => $value) {
                array_push($ids,intval($value['id']));
            }
            $whereArray['user_id'] = array('in',$ids);
            $this->assign('nickname',$nickname);
        }else{
            $whereArray = array();
        }
        $order = '';
        if($condition){
        	if($condition == 'number'){
        		$order = 'number desc';
        	}else if($condition == 'at_time'){
        		$order = 'at_time asc';
        	}
        	
        }
        $whereArray['a_id'] = $activity_id;
        $p = $p ? $p : 1;
        $count = M('Member_activity')->where($whereArray)->count();
        $whereArray['start'] = ($p - 1) * 20;
		$list = D('Memberactivity')->getList($whereArray,$order);

		$this->assign('p',$p);
        $this->assign('count',$count);
		$this->assign('list',$list);
        $this->assign('activity_id',$activity_id);
		$this->display();
	}
}