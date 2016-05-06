<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {

    public function index(){
        $data = json_encode($_REQUEST);
        echo 'success';
        open($data);
       // echo 'success';
   }
}