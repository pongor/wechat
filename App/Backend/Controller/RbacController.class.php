<?php
namespace Backend\Controller;

use Think\Controller;

/*
** 权限控制类 所有类均继承此类
*/
class RbacController extends Controller
{
    public function __construct(){
    	parent::__construct();
		$this->user_id = session('user_id') ;//? session('user_id') : redirect(U('Login/index'));
		if($this->user_id <=0){
			redirect(U('Login/index'));die;
		}else{
			self::rbac();
		}
    }
    // 获取用户拥有的权限 返回菜单列表  并将菜单列表缓存到文件中 使用S方法 缓存时间与session 存储的user_id一致
    public static function rbac(){
    	
    }

}