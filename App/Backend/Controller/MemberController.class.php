<?php
namespace Backend\Controller;

use Think\Controller;
class MemberController extends RbacController{
	public function index(){
		$nickname = I('get.nickname');
		$condition = I('get.condition');
        $p = I('get.p');
        if($nickname){
            $whereArray['nickname'] = array('like','%'.$nickname.'%');
            $this->assign('nickname',$nickname);
        }else{
            $whereArray = array();
        }
        $order = '';
        if($condition){
        	if($condition == 'integral'){
        		$order = 'integral desc';
        	}else if($condition == 'sign_up_time'){
        		$order = 'sign_up_time asc';
        	}
        	
        }
        $p = $p ? $p : 1;
        $count = M('Member')->where($whereArray)->count();
        $whereArray['start'] = ($p - 1) * 20;
		$list = D('Member')->getList($whereArray,$order);
		$this->assign('p',$p);
        $this->assign('count',$count);
		$this->assign('list',$list);
		$this->display();
	}
}