<?php
namespace Backend\Controller;
use Think\Controller;
class IndexController extends RbacController {
    public function index(){
    	$this->display();
    }
}