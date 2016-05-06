<?php
namespace Backend\Controller;
use Think\Controller;

class ActivityController extends Controller{
	public function index(){
		$this->display();
	}

	//详情页
	public function detail(){
		$id = intval(I('get.id'));
		if($id){
			//修改
			$result = D('Activity')->getField(array('id'=>$id));
			$this->assign('result',$result);
		}
		$this->display();
	}
}