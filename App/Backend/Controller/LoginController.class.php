<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/25
 * Time: 14:38
 */

namespace Backend\Controller;

use Think\Controller;

class LoginController extends Controller
{
    public function index(){
        $this->display();
    }

    public function login(){
        $username = I('post.username');
        $password = I('post.password');
        $data['username'] = $username;
        $data['password'] = md5($password);
        $r = D('User')->getInfo($data);
        if($r){
            // var_dump(2);die;
            session('user_id',$r['id'],3600);
            redirect(U('Index/index'));die;
        }else{
            redirect(U('Login/index'));die;
        }
    }
    public function logout(){
        session('user_id',null);
        redirect(U('Login/index'));die;
    }

    //ajax验证
    public function verify(){
        $username = trim(I('post.username'));
        $password = trim(I('post.password'));
        $data['username'] = $username;
        $data['password'] = md5($password);
        $r = D('User')->getInfo($data);
        if($r){
            // var_dump(2);die;
            session('user_id',$r['id'],3600);
            echo json_encode(array('status'=>1));die;
        }else{
            echo json_encode(array('status'=>0));die;
        }
    }

    public function register(){
        $this->display();
    }

    public function saveUser(){
        $data['username'] = trim(I('post.username'));
        $data['password'] = md5(trim(I('post.password')));
        $data['at_time'] = time();
        $data['lt_time'] = time();
        D('User')->add($data);
        redirect(U('Login/index'),0.5,'<script>alert("用户添加成功");</script>');die;
    }
}