<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {

    public function index(){
        $data = json_encode($_REQUEST);
        echo  $_GET["echostr"];
        open($data);
       // echo 'success';
   }
}